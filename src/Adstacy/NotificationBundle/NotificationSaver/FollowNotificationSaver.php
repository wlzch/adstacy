<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Comment;

class FollowNotificationSaver implements NotificationSaverInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @inheritdoc
     */
    public function save(User $from, User $to, $noun, $flush = false)
    {
        if ($from !== $to) {
            $notification = new Notification();
            $notification->setFrom($from);
            $notification->setTo($to);
            $notification->setType('follow');
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
        return $key == 'follow';
    }
}
