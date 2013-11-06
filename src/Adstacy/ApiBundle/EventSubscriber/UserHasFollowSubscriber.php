<?php

namespace Adstacy\ApiBundle\EventSubscriber;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Adstacy\AppBundle\Entity\User;

class UserHasFollowSubscriber implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof User && $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            if ($user->hasFollowUser($object)) {
                $event->getVisitor()->addData('followed', true);
            }
        }
    }
}
