<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Comment;

class CommentNotificationSaver implements NotificationSaverInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @inheritdoc
     */
    public function save(User $from, User $to, $comment, $flush = false)
    {
        if ($from !== $to) {
            $notification = new Notification();
            $notification->setFrom($from);
            $notification->setTo($to);
            $notification->setComment($comment);
            $notification->setAd($comment->getAd());
            $notification->setType('comment');
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
        return $noun instanceof Comment;
    }
}
