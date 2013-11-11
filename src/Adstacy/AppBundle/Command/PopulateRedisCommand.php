<?php

namespace Adstacy\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateRedisCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('adstacy:redis:populate')
            ->setDescription('Populate redis data')
            ->addArgument('what', InputArgument::REQUIRED, 'what to populate (available: all, user, tag, recommendation)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $what = $input->getArgument('what');
        $whats = explode(',', $what);
        if (in_array('all', $whats)) {
            $this->populateUserData($input, $output);
            $this->populateTagData($input, $output);
            $this->populateRecommendation($input, $output);
            $this->populateTrending($input, $output);
        } else {
            if (in_array('user', $whats)) {
                $this->populateUserData($input, $output);
            }
            if (in_array('tag', $whats)) {
                $this->populateTagData($input, $output);
            }
            if (in_array('recommendation', $whats)) {
                $this->populateRecommendation($input, $output);
            }
            if (in_array('trending', $whats)) {
                $this->populateTrending($input, $output);
            }
        }
    }

    private function populateUserData(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Populating user data...');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userManager = $this->getContainer()->get('adstacy.manager.user');

        $users = $em->createQuery('
            SELECT partial u.{id,username,realName,imagename,profilePicture}
            FROM AdstacyAppBundle:User u
        ')->getResult();

        $cnt = 0;
        foreach ($users as $user) {
            $userManager->saveToRedis($user);
            $cnt++;
        }

        $output->writeln($cnt.' user data populated');
    }

    private function populateTagData(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Populating tag data...');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $redis = $this->getContainer()->get('snc_redis.default');

        $ads = $em->createQuery('
            SELECT partial a.{id,tags}
            FROM AdstacyAppBundle:Ad a
        ')->getResult();
        $tags = array();

        $cnt = 0;
        foreach ($ads as $ad) {
            $adTags = $ad->getTags();
            foreach ($adTags as $tag) {
              $tags[$tag] = (isset($tags[$tag]) ? $tags[$tag] : 0) + 1;
            }
        }

        $recommendation = array();
        foreach ($tags as $tag => $value) {
            $input = substr($tag, 0, 1);
            for ($i = 1, $len = strlen($tag); $i < $len; $i++) {
                $input .= $tag[$i];
                $recommendation[$input] = 0;
                $cnt++;
            }
            $recommendation[$tag.'*'] = 0;
            $cnt++;
        }
        $redis->del('tags');
        $redis->del('all_tags');
        $cmd = $redis->createCommand('zadd');
        $cmd->setArguments(array('tags', $recommendation));
        $redis->executeCommand($cmd);
        $cmd = $redis->createCommand('zadd');
        $cmd->setArguments(array('all_tags', $tags));
        $redis->executeCommand($cmd);

        $output->writeln($cnt.' suggestion tags data populated');
        $output->writeln(count($tags). ' tags populated');
    }

    private function populateRecommendation(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Populating recommendation data');
        $container = $this->getContainer();
        $repo = $container->get('doctrine')->getRepository('AdstacyAppBundle:User');
        $em = $container->get('doctrine')->getManager();
        $userManager = $container->get('adstacy.manager.user');
    
        $query = $em->createQuery('
            SELECT partial u.{id,username}, partial f.{id}
            FROM AdstacyAppBundle:User u
            LEFT JOIN u.followings f
        ');

        // recommend the most common followings
        $cnt = 0;
        foreach ($query->getResult() as $user) {
            if ($user->getUsername()) {
                $userManager->suggestFollow($user);
                $cnt++;
            }
        }
        $output->writeln("$cnt data populated");
    }

    private function populateTrending(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Populating trending ads');
        $container = $this->getContainer();
        $repo = $container->get('doctrine')->getRepository('AdstacyAppBundle:Ad');
        $redis = $this->getContainer()->get('snc_redis.default');

        $ids = array();
        foreach ($repo->findTrendingPromotes(100) as $ad) {
            $ids[$ad->getId()] = $ad->getPromoteesCount();;
        }

        $redis->del('trending');
        $cmd = $redis->createCommand('zadd');
        $cmd->setArguments(array('trending', $ids));
        $redis->executeCommand($cmd);

        $output->writeln(count($ids).' trending data populated');
    }
}
