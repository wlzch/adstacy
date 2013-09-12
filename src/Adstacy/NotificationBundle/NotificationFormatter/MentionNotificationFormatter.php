<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Adstacy\NotificationBundle\Entity\Notification;

class MentionNotificationFormatter implements NotificationFormatterInterface
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
        return $this->translator->trans('notification.mention', array(
            '%url_ad%' => $this->router->generate('adstacy_app_ad_show', array(
                'id' => $notification->getAd()->getId()
            )),
            '%url_user%' => $this->router->generate('adstacy_app_user_profile', array(
                'username' => $notification->getFrom()->getUsername()
            )),
            '%user_from%' => $notification->getFrom()->getUsername(),
            '%comment%' => $notification->getComment()->getContent()
        ));
    }

    public function support(Notification $notification)
    {
        return $notification->getType() == 'mention';
    }
}
