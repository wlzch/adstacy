<?php

namespace Adstacy\NotificationBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailManager
{
    private $container;
    private $notificationManager;

    public function __construct(ContainerInterface $container, NotificationManager $notificationManager)
    {
        $this->container = $container;
        $this->notificationManager = $notificationManager;
    }

    public function renderEmails($notifications = array())
    {
        $filteredNotifications = array();
        foreach ($notifications as $notification) {
            $filteredNotifications[] = $this->notificationManager->format($notification);
        }
        $html = $this->container->get('templating')->render(
            'AdstacyNotificationBundle::email.html.twig', array(
                'notifications' => $filteredNotifications
            )
        );

        return $html;
    }
}
