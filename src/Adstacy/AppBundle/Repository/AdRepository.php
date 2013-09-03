<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Adstacy\AppBundle\Entity\User;

/**
 * AdRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdRepository extends EntityRepository
{

    /**
     * Find $limit ads with id lower than $since
     *
     * @param integer|null $since
     * @param integer $limit
     *
     * @return array
     */
    public function findAdsSinceId($since, $limit = 30)
    {
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder()
            ->select(array(
              'partial p.{id,imagename,description,tags,thumbHeight,promoteesCount,created}',
              'partial u.{id,username,imagename,realName}'
            ))
            ->from('AdstacyAppBundle:Ad', 'p')
            ->innerJoin('p.user', 'u')
            ->setMaxResults($limit)
            ->orderBy('p.id', 'DESC')
        ;
        if ($since) {
            $builder->andWhere('p.id < :id')
                ->setParameter('id', $since)
            ;
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * Find all by $user query join promotees
     *
     * @param User $user
     *
     * @return Query
     */
    public function findByUserJoinPromoteQuery(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id,imagename,description,tags,thumbHeight,promoteesCount,created},
            partial u.{id,username,imagename,realName},
            partial fi.{id,username,imagename,realName},
            fip
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            LEFT JOIN a.promotees pa
            LEFT JOIN pa.user pu
            LEFT JOIN u.followings fi
            LEFT JOIN fi.promotes fip
            WHERE u.id = :id OR pu.id = :id
        ');

        return $query->setParameter('id', $user->getId());
    }

    /**
     * Find $limit ads by $user
     *
     * @param User $user
     * @param integer limit
     *
     * @return Query
     */
    public function findByUserQuery(User $user, $limit = 20)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id,imagename,description,tags,thumbHeight,promoteesCount,created}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            WHERE u.id = :id
        ');
        $query->setMaxResults($limit);

        return $query->setParameter('id', $user->getId());
    }

    /**
     * Find $limit ads by $user
     *
     * @param User $user
     * @param integer limit
     *
     * @return array
     */
    public function findByUser(User $user, $limit = 20)
    {
        return $this->findByUserQuery($user, $limit)->getResult();
    }

    /**
     * Find user's stream Query
     *
     * @param User $user
     *
     * @return Query
     */
    public function findUserStreamQuery(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id,imagename,description,tags,thumbHeight,promoteesCount,created},
            partial u.{id,username,imagename,realName},
            partial fi.{id,username,imagename,realName},
            fip
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            LEFT JOIN u.followers fe
            LEFT JOIN u.followings fi
            LEFT JOIN a.promotees ap
            LEFT JOIN ap.user apu
            LEFT JOIN fi.promotes fip
            LEFT JOIN fip.user pu
            WHERE u.id = :id OR pu.id = :id OR fe.id = :id OR apu.id = :id
            ORDER BY a.created DESC
        ');
        $query->useResultCache(true, 300, 'AdFindUserStreamQuery');

        return $query->setParameter('id', $user->getId());
    }
}
