<?php

namespace Adstacy\NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Adstacy\AppBundle\Entity\User;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends EntityRepository
{
    /**
     * Find notifications for $user
     *
     * @param User $user
     * @param null|false get all/only unread notifications
     *
     * @return array
     */
    public function findNotificationsForUserQuery(User $user, $read = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select(array(
                'n',
                'partial f.{id,username,imagename,realName}',
                'partial a.{id,imagename,description,created}',
                'c'
            ))
            ->from('AdstacyNotificationBundle:Notification', 'n')
            ->innerJoin('n.to', 'u')
            ->innerJoin('n.from', 'f')
            ->leftJoin('n.ad', 'a')
            ->leftJoin('n.comment', 'c')
            ->where('u.id = :id')
            ->orderBy('n.created', 'DESC')
        ;
        if ($read === false) {
            $qb->andWhere('n.read = FALSE');
        }

        return $qb->setParameter('id', $user->getId())->getQuery();
    }
}
