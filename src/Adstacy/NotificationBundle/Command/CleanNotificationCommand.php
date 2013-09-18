<?php

namespace Adstacy\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('adstacy:notification:clean')
            ->setDescription('Clean notification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('Cleaning notification...');

        $since = date('Y-m-d', strtotime('-10 day', time())); // 7 days ago
        $em->createQuery('
            DELETE FROM AdstacyNotificationBundle:Notification n
            WHERE n.created <= :since
        ')->setParameter('since', $since)->execute();

        $output->writeln('Notification cleaned');
    }
}
