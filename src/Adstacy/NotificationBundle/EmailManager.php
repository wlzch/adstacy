<?php

namespace Adstacy\NotificationBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailManager
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function renderEmails($notifications = array())
    {
        $html = $this->container->get('templating')->render(
            'AdstacyNotificationBundle::email.html.twig', array(
                'notifications' => $notifications
            )
        );

        return $html;
    }
}
