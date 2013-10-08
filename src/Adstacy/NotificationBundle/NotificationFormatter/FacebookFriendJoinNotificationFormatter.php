<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Helper\Formatter;
use Adstacy\AppBundle\Helper\UserHelper;

class FacebookFriendJoinNotificationFormatter implements NotificationFormatterInterface
{
    private $router;
    private $translator;
    private $formatter;
    private $userHelper;

    public function __construct(Router $router, Translator $translator, Formatter $formatter, UserHelper $userHelper)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->formatter = $formatter;
        $this->userHelper = $userHelper;
    }

    public function getImage(Notification $notification, $absolute = false)
    {
        return $this->userHelper->getProfilePicture($notification->getFrom(), $absolute);
    }

    public function getTime(Notification $notification)
    {
        return $this->formatter->ago($notification->getCreated());
    }

    public function getName(Notification $notification)
    {
        return $notification->getFrom()->getUsername();
    }

    public function getUrl(Notification $notification, $absolute = false)
    {
        return $this->router->generate('adstacy_app_user_profile', array(
            'username' => $notification->getFrom()->getUsername(),
        ), $absolute);
    }

    public function getText(Notification $notification)
    {
        return $this->translator->trans('notification.facebook_friend_join');
    }

    public function support(Notification $notification)
    {
        return $notification->getType() == 'facebook_friend_join';
    }
}
