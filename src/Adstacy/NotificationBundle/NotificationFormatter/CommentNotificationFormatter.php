<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Helper\Formatter;

class CommentNotificationFormatter implements NotificationFormatterInterface
{
    private $router;
    private $translator;
    private $formatter;

    public function __construct(Router $router, Translator $translator, Formatter $formatter)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->formatter = $formatter;
    }

    public function format(Notification $notification)
    {
        return $this->translator->trans('notification.comment', array(
            '%url_ad%' => $this->router->generate('adstacy_app_ad_show', array(
                'id' => $notification->getAd()->getId()
            )).'#comments-'.$notification->getComment()->getId(),
            '%url_user%' => $this->router->generate('adstacy_app_user_profile', array(
                'username' => $notification->getFrom()->getUsername()
            )),
            '%user_from%' => $notification->getFrom()->getUsername(),
            '%comment%' => $this->formatter->more($notification->getComment()->getContent(), 50)
        ));
    }

    public function support(Notification $notification)
    {
        return $notification->getType() == 'comment';
    }
}
