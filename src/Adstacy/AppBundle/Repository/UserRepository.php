<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Cache\ApcCache;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Ad;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{

    /**
     * @override
     *
     * @param integer id
     *
     * @return User
     */
    public function find($id)
    {
        if (count(func_get_args()) > 1) {
            return parent::find(func_get_args());
        }
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT u FROM AdstacyAppBundle:User WHERE u.id = :id');
        $query->useResultCache(true, 3600, 'UserFind');

        return $query->setParameter('id', $id)->getSingleResult();
    }

    /**
     * @override
     *
     * @param integer id
     *
     * @return User
     */
    public function findOneById($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT u FROM AdstacyAppBundle:User WHERE u.id = :id');
        $query->useResultCache(true, 3600, 'UserFind');

        return $query->setParameter('id', $id)->getSingleResult();
    }


    /**
     * Find $user followers query
     *
     * @param User $user
     *
     * @return Query
     */
    public function findFollowersByUserQuery(User $user)
    {
        $em = $this->getEntitymanager();
        $query = $em->createQuery('
            SELECT partial u.{id,username,imagename,realName,adsCount,followersCount,profilePicture},
            partial a.{id,imagename,thumbHeight,imageWidth,imageHeight}
            FROM AdstacyAppBundle:User u
            JOIN u.followings f
            LEFT JOIN u.ads a
            WHERE f.id = :id
        ');

        return $query->setParameter('id', $user->getId());
    }

    /**
     * Find $user followers
     *
     * @param User $user
     *
     * @return array
     */
    public function findFollowersByUser(User $user)
    {
        return $this->findFollowersByUserQuery($user)->getResult();
    }

    /**
     * Find $user followings query
     *
     * @param User $user
     *
     * @return Query
     */
    public function findFollowingsByUserQuery(User $user)
    {
        $em = $this->getEntitymanager();
        $query = $em->createQuery('
            SELECT partial u.{id,username,imagename,realName,adsCount,followersCount,profilePicture},
            partial a.{id,imagename,thumbHeight,imageHeight,imageWidth}
            FROM AdstacyAppBundle:User u
            JOIN u.followers f
            LEFT JOIN u.ads a
            WHERE f.id = :id
        ');

        return $query->setParameter('id', $user->getId());
    }

    /**
     * Find $user followings
     *
     * @param User $user
     *
     * @return array
     */
    public function findFollowingsByUser(User $user)
    {
        return $this->findFollowingsByUserQuery($user)->getResult();
    }

    /**
     * Find users who promotes $ad 
     *
     * @param integer id
     *
     * @return array
     */
    public function findPromotesByAd(Ad $ad)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial u.{id,username,imagename,realName,adsCount,followersCount,profilePicture},
            partial a.{id,imagename,thumbHeight,imageHeight,imageWidth}
            FROM AdstacyAppBundle:User u
            JOIN u.promotes up
            JOIN up.ad ad
            LEFT JOIN u.ads a
            WHERE ad.id = :id
        ');

        return $query->setParameter('id', $ad->getId());
    }

    /**
     * Suggest users
     *
     */
    public function suggestUserQuery(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial u.{id,username,imagename,realName,adsCount,followersCount,profilePicture},
            partial a.{id,imagename,thumbHeight,imageHeight,imageWidth}
            FROM AdstacyAppBundle:User u
            LEFT JOIN u.ads a
            WHERE u.id <> :id
            ORDER BY u.followersCount DESC
        ');
        $query->useResultCache(true, 7200, 'UserSuggestUserQuery');

        return $query->setParameter('id', $user->getId());
    }

    /**
     * Find users by username
     *
     * @param array $usernames
     *
     * @return array
     */
    public function findByUsernames($usernames = array())
    {
        $em = $this->getEntityManager();
        $formatted = strtolower("'".implode("', '", $usernames)."'");

        return $em->createQuery("
            SELECT partial u.{id,username,imagename,realName,adsCount,followersCount,notificationsCount,profilePicture}
            FROM AdstacyAppBundle:User u
            WHERE u.usernameCanonical IN ($formatted)
        ")->getResult();
    }
}
