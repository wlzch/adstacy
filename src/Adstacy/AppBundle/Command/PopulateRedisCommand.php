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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $redis = $this->getContainer()->get('snc_redis.default');

        $users = $em->createQuery('
            SELECT u.username, u.realName
            FROM AdstacyAppBundle:User u
        ')->getScalarResult();

        $output->writeln('Populating redis data...');
        $cnt = 0;
        foreach ($users as $user) {
            $username = $user['username'];
            $input = substr($username, 0, 1);
            for ($i = 1, $len = strlen($username); $i < $len; $i++) {
                $input .= $username[$i];
                $redis->zadd('usernames', 0, $input);
                $cnt++;
            }
            $redis->zadd('usernames', 0, $username.'*');
            $redis->hmset("user:$username", 'name', $user['realName'], 'username', $user['username']);
            $cnt++;
        }

        $output->writeln($cnt.' data populated');
    }
}
