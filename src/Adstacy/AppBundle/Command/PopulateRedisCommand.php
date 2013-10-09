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
        }
    }

    private function populateUserData(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Populating user data...');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $redis = $this->getContainer()->get('snc_redis.default');
        $userHelper = $this->getContainer()->get('adstacy.helper.user');

        $users = $em->createQuery('
            SELECT partial u.{id,username,realName,imagename,profilePicture}
            FROM AdstacyAppBundle:User u
        ')->getResult();

        $cnt = 0;
        foreach ($users as $user) {
            $username = $user->getUsername();
            $input = substr($username, 0, 1);
            for ($i = 1, $len = strlen($username); $i < $len; $i++) {
                $input .= $username[$i];
                $redis->zadd('usernames', 0, $input);
                $cnt++;
            }
            $redis->zadd('usernames', 0, $username.'*');
            $redis->hmset("user:$username", 
                'id', $user->getId(),
                'name', $user->getRealName(),
                'avatar', $userHelper->getProfilePicture($user, false),
                'value', '@'.$user->getUsername()
            );
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
              $tags[$tag] = 1;
            }
        }

        foreach ($tags as $tag => $value) {
            $input = substr($tag, 0, 1);
            for ($i = 1, $len = strlen($tag); $i < $len; $i++) {
                $input .= $tag[$i];
                $redis->zadd('tags', 0, $input);
                $cnt++;
            }
            $redis->zadd('tags', 0, $tag.'*');
            $cnt++;
        }

        $output->writeln($cnt.' tags data populated');
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
            JOIN u.followings f
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
}
