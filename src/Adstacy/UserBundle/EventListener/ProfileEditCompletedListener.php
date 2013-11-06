<?php

namespace Adstacy\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Adstacy\AppBundle\Manager\UserManager;

/**
 * Listener for profile edit completed
 */
class ProfileEditCompletedListener implements EventSubscriberInterface
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onProfileEditCompleted',
        );
    }

    public function onProfileEditCompleted(FilterUserResponseEvent $event)
    {
        $this->userManager->saveToRedis($event->getUser());
    }
}
