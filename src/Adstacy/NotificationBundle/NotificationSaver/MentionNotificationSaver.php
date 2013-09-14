<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Doctrine\ORM\EntityManager;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Comment;

class MentionNotificationSaver implements NotificationSaverInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function save(User $from, User $to, $comment, $flush = false)
    {
        $matches = null;
        preg_match_all('/@([^@ ]+)/', $comment->getContent(), $matches);
        $usernames = array_unique($matches[1]);
        if (count($usernames) > 0) {
            $usernames = array_filter($usernames, function($username) use (&$from) {
                return strtolower($username) != strtolower($from->getUsername());
            });
            $users = $this->em->getRepository('AdstacyAppBundle:User')->findByUsernames($usernames);
            foreach ($users as $user) {
                $notification = new Notification();
                $notification->setFrom($from);
                $notification->setTo($user);
                $notification->setComment($comment);
                $notification->setAd($comment->getAd());
                $notification->setType('mention');
                $user->addNotification($notification);
                $this->em->persist($notification);
                $this->em->persist($user);
            }
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
