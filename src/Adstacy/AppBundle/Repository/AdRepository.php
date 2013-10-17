<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Helper\Formatter;

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
    public function findAdsSinceId($since, $limit = 10)
    {
        if ($since == null) {
            $since = 2000000000; // if no id, set it to a very high id
        }
        $em = $this->getEntityManager();
        $idsQuery = $em->createQuery("
            SELECT a
            FROM AdstacyAppBundle:Ad a
            WHERE a.id < :id
            ORDER BY a.id DESC
        ");
        $idsQuery->setMaxResults($limit)->setParameter('id', $since);
        $ids = array();
        foreach ($idsQuery->getResult() as $ad) {
            $ids[] = $ad->getId();
        }

        return $this->getAdsAndCommentsIn($ids);
    }

    /**
     * Find ads $user promoted
     *
     * @param User $user
     * @param integer $since
     * @param integer $limit
     *
     * @return array
     */
    public function findByPromote(User $user, $since = null, $limit = 10)
    {
        if ($since == null) {
            $since = 2000000000;
        }
        $em = $this->getEntityManager();
        $filterQuery = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            LEFT JOIN a.promotees pa
            LEFT JOIN pa.user pu
            WHERE a.id < :since AND pu.id = :id AND a.active = TRUE
            ORDER BY a.id DESC
        ');
        $ids = array();
        foreach ($filterQuery->setParameter('since', $since)->setParameter('id', $user->getId())->getArrayResult() as $ad) {
            $ids[] = $ad['id'];
        }

        return $this->getAdsAndCommentsIn($ids);
    }

    /**
     * Find all promotes by $user
     *
     * @param User $user
     *
     * @return Query
     */
    public function findByUserJoinPromoteQuery(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT a,
            partial u.{id,username,imagename,realName,profilePicture}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            LEFT JOIN a.promotees pa
            LEFT JOIN pa.user pu
            WHERE u.id = :id OR pu.id = :id AND a.active = TRUE
        ');

        return $query->setParameter('id', $user->getId());
    }
    /**
     * Find $limit ads by $user since last id $since
     *
     * @param User $user
     * @param integer $since
     * @param integer limit
     *
     * @return array
     */
    public function findByUser(User $user, $since = null, $limit = 20)
    {
        if ($since == null) {
            $since = 2000000000;
        }
        $em = $this->getEntityManager();
        $filterQuery = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            WHERE a.id < :since AND u.id = :id AND a.active = TRUE
            ORDER BY a.id DESC
        ');
        $ids = array();
        foreach ($filterQuery->setParameter('since', $since)->setParameter('id', $user->getId())->getArrayResult() as $ad) {
            $ids[] = $ad['id'];
        }

        return $this->getAdsAndCommentsIn($ids);
    }

    /**
     * Find user's stream Query
     *
     * @param User $user
     * @param integer since id
     *
     * @return Query
     */
    public function findUserStream(User $user, $since = null, $limit = 10)
    {
        $em = $this->getEntityManager();
        if ($since == null) {
            $since = 2000000000;
        }
        $filterQuery = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            JOIN u.followers f
            LEFT JOIN a.promotees ap
            LEFT JOIN ap.user apu
            WHERE a.id < :since AND (u.id = :id OR f.id = :id OR apu.id = :id AND a.active = TRUE)
            ORDER BY a.id DESC
        ');
        $filterQuery->setParameter('id', $user->getId())->setParameter('since', $since)->setMaxResults($limit);
        $ids = array();
        foreach ($filterQuery->getArrayResult() as $ad) {
            $ids[] = $ad['id'];
        }

        return $this->getAdsAndCommentsIn($ids);
    }

    public function findTrendingPromotes($limit = 50)
    {
        $em = $this->getEntityManager();
        $rsm = $this->getNativeSqlMapping();
        $selectSql = $this->getAdSelectSql();
        $filterSql = $this->getCommentFilterSql();
        $query = $em->createNativeQuery("
            $selectSql
            FROM ad a
            INNER JOIN users u ON a.user_id = u.id
            LEFT JOIN ad_comment c ON c.ad_id = a.id
            WHERE a.active = TRUE AND ($filterSql)
            ORDER BY a.promotees_count DESC
            LIMIT $limit
        ", $rsm);

        return $query->getResult();
    }

    /**
     * Find all ads with most promote since $since
     *
     * @param Datetime $since
     * @param integer limit
     *
     * @return array (0 => Ad, 1 => cnt)
     */
    public function findTrendingSince($since, $limit = 50)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT a, u, c, COUNT(ap.created) as cnt
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            JOIN a.promotees ap
            LEFT JOIN a.comments c
            WHERE ap.created >= :since AND a.active = TRUE
            GROUP BY a.id, u.id
            ORDER BY cnt DESC
        ');
        $query->setMaxResults($limit);
        $query->useResultCache(true, 3600, 'AdFindTrendingSince');

        return $query->setParameter('since', $since)->getResult();
    }

    private function getNativeSqlMapping()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Adstacy\AppBundle\Entity\Ad', 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'type', 'type');
        $rsm->addFieldResult('a', 'imagename', 'imagename');
        $rsm->addFieldResult('a', 'title', 'title');
        $rsm->addFieldResult('a', 'youtubeId', 'youtubeId');
        $rsm->addFieldResult('a', 'description', 'description');
        $rsm->addFieldResult('a', 'tags', 'tags');
        $rsm->addFieldResult('a', 'thumb_height', 'thumbHeight');
        $rsm->addFieldResult('a', 'image_width', 'imageWidth');
        $rsm->addFieldResult('a', 'image_height', 'imageHeight');
        $rsm->addFieldResult('a', 'promotees_count', 'promoteesCount');
        $rsm->addFieldResult('a', 'active', 'active');
        $rsm->addFieldResult('a', 'created', 'created');
        $rsm->addFieldResult('a', 'updated', 'updated');
        $rsm->addJoinedEntityResult('Adstacy\AppBundle\Entity\User', 'u', 'a', 'user');
        $rsm->addFieldResult('u', 'u_id', 'id');
        $rsm->addFieldResult('u', 'u_username', 'username');
        $rsm->addFieldResult('u', 'u_imagename', 'imagename');
        $rsm->addFieldResult('u', 'u_real_name', 'realName');
        $rsm->addFieldResult('u', 'u_profile_picture', 'profilePicture');
        $rsm->addJoinedEntityResult('Adstacy\AppBundle\Entity\Comment', 'c', 'a', 'comments');
        $rsm->addFieldResult('c', 'c_id', 'id');
        $rsm->addFieldResult('c', 'c_content', 'content');
        $rsm->addFieldResult('c', 'c_created', 'created');

        return $rsm;
    }

    private function getAdSelectSql()
    {
        return 'SELECT a.*,
            u.id as u_id, u.username as u_username, u.imagename as u_imagename, u.real_name as u_real_name,
            u.profile_picture as u_profile_picture,
            c.id as c_id, c.content as c_content, c.created as c_created
        ';
    }

    private function getCommentFilterSql()
    {
        return 'a.comments_count = 0 OR (
                   a.comments_count > 0 AND c.id IN (
                       SELECT c0.id
                       FROM ad_comment c0
                       WHERE c0.ad_id = a.id
                       ORDER BY c0.created DESC
                       LIMIT 2
               )
            )
        ';
    }

    /**
     * Get ads with 2 comments which have id in $ids
     *
     * @param array $ids
     *
     * @return array
     */
    private function getAdsAndCommentsIn($ids = array())
    {
        if (count($ids) <= 0) {
            return array();
        }
        $em = $this->getEntityManager();
        $rsm = $this->getNativeSqlMapping();
        $selectSql = $this->getAdSelectSql();
        $filterSql = $this->getCommentFilterSql();
        $ids = Formatter::arrayToSql($ids);
        $query = $em->createNativeQuery("
            $selectSql
            FROM ad a
            INNER JOIN users u ON a.user_id = u.id 
            LEFT JOIN ad_comment c ON c.ad_id = a.id
            WHERE a.id IN $ids AND ($filterSql)
            ORDER BY a.id DESC
        ", $rsm);

        return $query->getResult();
    }
}
