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
            ->setName('adstacy:redis:populate-user')
            ->setDescription('Populate redis user data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $redis = $this->getContainer()->get('snc_redis.default');
        $userHelper = $this->getContainer()->get('adstacy.helper.user');

        $users = $em->createQuery('
            SELECT partial u.{id,username,realName,imagename,profilePicture}
            FROM AdstacyAppBundle:User u
        ')->getResult();

        $output->writeln('Populating redis data...');
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

        $output->writeln($cnt.' data populated');
    }
}
