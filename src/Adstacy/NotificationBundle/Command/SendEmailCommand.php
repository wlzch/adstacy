<?php

namespace Adstacy\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('adstacy:notification:send-email')
            ->setDescription('Send notification email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $context = $container->get('router')->getContext();
        $context->setHost('www.adstacy.com');
        $context->setScheme('http');

        $output->writeln('Sending notification email...');

        $since = date('Y-m-d', strtotime('-12 hours', time()));
        $notifications = $em->createQuery('
            SELECT n
            FROM AdstacyNotificationBundle:Notification n
            WHERE n.created >= :since
        ')->setParameter('since', $since)->getResult();

        $mailer = $container->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $container->get('swiftmailer.transport.real');
        $emailManager = $container->get('adstacy.notification.email_manager');

        $groupedNotifications = array();
        foreach ($notifications as $notification) {
            $username = $notification->getTo()->getUsername();
            if (!isset($groupedNotifications[$username])) {
                $groupedNotifications[$username] = array();
            }
            $groupedNotifications[$username][] = $notification;
        }

        foreach ($groupedNotifications as $notifications) {
            $html = $emailManager->renderEmails($notifications);
            $message = \Swift_Message::newInstance()
                ->setSubject('Notifications')
                ->setFrom($container->getParameter('adstacy.mail.updates'))
                ->setTo($notification->getTo()->getEmail())
                ->setBody($html, 'text/html')
            ;
            $spool->queueMessage($message);
        }

        $sent = $spool->flushQueue($transport);

        $output->writeln("$sent emails sent");
    }
}
