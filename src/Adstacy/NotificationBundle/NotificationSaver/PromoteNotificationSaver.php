<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Comment;
use Adstacy\AppBundle\Entity\Ad;

class PromoteNotificationSaver implements NotificationSaverInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @inheritdoc
     */
    public function save(User $from, User $to, $ad, $flush = false)
    {
        if ($from !== $to ) {
            $notification = new Notification();
            $notification->setFrom($from);
            $notification->setTo($to);
            $notification->setAd($ad);
            $notification->setType('promote');
            $to->addNotification($notification);
            $this->om->persist($notification);
            $this->om->persist($to);
            if ($flush) {
                $this->om->flush();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function support($noun, $key = false)
    {
        return $noun instanceof Ad && $key == 'promote';
    }
}
