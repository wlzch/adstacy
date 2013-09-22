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
        $router = $this->container->get('router');
        $userHelper = $this->container->get('adstacy.helper.user');
        $formatter = $this->container->get('adstacy.helper.formatter');

        $filteredNotifications = array();
        foreach ($notifications as $notification) {
            $notif = array();
            $notif['profile_pic'] = $userHelper->getProfilePicture($notification->getFrom(), true);
            $notif['time'] = $formatter->ago($notification->getCreated());
            $notif['username'] = $notification->getFrom()->getUsername();
            $type = $notification->getType();
            if ($type == 'follow') {
                $notif['text'] = 'is following you';
                $notif['url'] = $router->generate('adstacy_app_user_profile', array('username' => $notification->getFrom()->getUsername()), true);
            } elseif ($type == 'comment') {
                $notif['text'] = 'commented on your ad: '.$formatter->more($notification->getComment()->getContent(), 50);
                $notif['url'] = $router->generate('adstacy_app_ad_show', array('id' => $notification->getAd()->getId()), true);
            } elseif ($type == 'mention') {
                $notif['text'] = 'mentioned you on comment: '.$formatter->more($notification->getComment()->getContent(), 50);
                $notif['url'] = $router->generate('adstacy_app_ad_show', array('id' => $notification->getAd()->getId()), true);
            } elseif ($type == 'promote') {
                $notif['text'] = 'promoted your ad';
                $notif['url'] = $router->generate('adstacy_app_ad_show', array('id' => $notification->getAd()->getId()), true);
            }
            $filteredNotifications[] = $notif;
        }
        $html = $this->container->get('templating')->render(
            'AdstacyNotificationBundle::email.html.twig', array(
                'notifications' => $filteredNotifications
            )
        );

        return $html;
    }
}
