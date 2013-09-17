<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Helper\Formatter;
use Adstacy\AppBundle\Helper\UserHelper;

class FollowNotificationFormatter implements NotificationFormatterInterface
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

    public function format(Notification $notification)
    {
        return $this->translator->trans('notification.follow', array(
            '%url_user%' => $this->router->generate('adstacy_app_user_profile', array(
                'username' => $notification->getFrom()->getUsername()
            )),
            '%profile_pic%' => $this->userHelper->getProfilePicture($notification->getFrom()),
            '%time%' => $this->formatter->ago($notification->getCreated()),
            '%user_from%' => $notification->getFrom()->getUsername(),
        ));
    }

    public function support(Notification $notification)
    {
        return $notification->getType() == 'follow';
    }
}
