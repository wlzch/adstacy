<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Adstacy\NotificationBundle\Entity\Notification;

class FollowNotificationFormatter implements NotificationFormatterInterface
{
    private $router;
    private $translator;

    public function __construct(Router $router, Translator $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function format(Notification $notification)
    {
        return $this->translator->trans('notification.follow', array(
            '%url_user%' => $this->router->generate('adstacy_app_user_profile', array(
                'username' => $notification->getFrom()->getUsername()
            )),
            '%user_from%' => $notification->getFrom()->getUsername(),
        ));
    }

    public function support(Notification $notification)
    {
        return $notification->getType() == 'follow';
    }
}
