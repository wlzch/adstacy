<?php

namespace Adstacy\AppBundle\EventSubscriber;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Entity\Image;

class ImageSerializerSubscriber implements EventSubscriberInterface
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
        $userHelper = $this->container->get('adstacy.helper.user');
        $uploaderHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        if ($object instanceof User) {
            $event->getVisitor()->addData('photo', $userHelper->getProfilePicture($object));
        } elseif ($object instanceof Ad && $object->getType() == 'image') {
            $event->getVisitor()->addData('image', $uploaderHelper->asset($object, 'image'));
        }
    }
}
