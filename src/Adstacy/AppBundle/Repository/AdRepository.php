<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
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
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            WHERE a.id < :since
            ORDER BY a.id DESC
        ');

        return $this->getAdsAndCommentsIn($query, $since, $limit);
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
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            LEFT JOIN a.promotees pa
            LEFT JOIN pa.user pu
            WHERE a.id < :since AND pu.id = :id AND a.active = TRUE
            ORDER BY a.id DESC
        ');

        return $this->getAdsAndCommentsIn($query->setParameter('id', $user->getId()), $since, $limit);
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
    public function findByUser(User $user, $since = null, $limit = 10)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT partial a.{id}
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            WHERE a.id < :since AND u.id = :id AND a.active = TRUE
            ORDER BY a.id DESC
        ');

        return $this->getAdsAndCommentsIn($query->setParameter('id', $user->getId()), $since, $limit);
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
        $query = $em->createQuery('
            SELECT DISTINCT a.id
            FROM AdstacyAppBundle:Ad a
            JOIN a.user u
            JOIN u.followers f
            LEFT JOIN a.promotees ap
            LEFT JOIN ap.user apu
            WHERE a.id < :since AND (u.id = :id OR f.id = :id OR apu.id = :id AND a.active = TRUE)
            ORDER BY a.id DESC
        ');

        return $this->getAdsAndCommentsIn($query->setParameter('id', $user->getId()), $since, $limit);
    }

    public function findTrendingPromotes($limit = 50)
    {
        $em = $this->getEntityManager();
        $rsm = $this->getNativeSqlMapping();
        $query = $em->createNativeQuery("
            SELECT a.*,
                u.id as u_id, u.username as u_username, u.imagename as u_imagename, u.real_name as u_real_name,
                u.profile_picture as u_profile_picture,
                c.id as c_id, c.content as c_content, c.created as c_created,
                cu.id as cu_id, cu.username as cu_username, cu.imagename as cu_imagename, cu.real_name as cu_real_name,
                cu.profile_picture as cu_profile_picture
            FROM ad a
            INNER JOIN users u ON a.user_id = u.id
            LEFT JOIN ad_comment c ON c.ad_id = a.id
            LEFT JOIN users cu ON c.user_id = cu.id
            WHERE a.active = TRUE AND (
                a.comments_count = 0 OR (
                   a.comments_count > 0 AND c.id IN (
                       SELECT c0.id
                       FROM ad_comment c0
                       WHERE c0.ad_id = a.id
                       ORDER BY c0.created DESC
                       LIMIT 2
                   )
                )
            )
            ORDER BY a.promotees_count DESC
            LIMIT $limit
        ", $rsm);

        return $query->getResult();
    }

    /**
     * NOT USED
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

    /**
     * Find ads having id in $ids
     *
     * @param array $ids
     * @param string $order
     */
    public function findByIds($ids = array(), $order = null)
    {
        return $this->_getAdsAndCommentsIn($ids, $order);
    }


    private function getNativeSqlMapping()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Adstacy\AppBundle\Entity\Ad', 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'type', 'type');
        $rsm->addFieldResult('a', 'imagename', 'imagename');
        $rsm->addFieldResult('a', 'title', 'title');
        $rsm->addFieldResult('a', 'youtube_url', 'youtubeUrl');
        $rsm->addFieldResult('a', 'description', 'description');
        $rsm->addFieldResult('a', 'tags', 'tags');
        $rsm->addFieldResult('a', 'image_width', 'imageWidth');
        $rsm->addFieldResult('a', 'image_height', 'imageHeight');
        $rsm->addFieldResult('a', 'promotees_count', 'promoteesCount');
        $rsm->addFieldResult('a', 'comments_count', 'commentsCount');
        $rsm->addFieldResult('a', 'active', 'active');
        $rsm->addFieldResult('a', 'created', 'created');
        $rsm->addFieldResult('a', 'updated', 'updated');
        $rsm->addFieldResult('a', 'last_promotees', 'lastPromotees');
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
        $rsm->addJoinedEntityResult('Adstacy\AppBundle\Entity\User', 'cu', 'c', 'user');
        $rsm->addFieldResult('cu', 'cu_id', 'id');
        $rsm->addFieldResult('cu', 'cu_username', 'username');
        $rsm->addFieldResult('cu', 'cu_imagename', 'imagename');
        $rsm->addFieldResult('cu', 'cu_real_name', 'realName');
        $rsm->addFieldResult('cu', 'cu_profile_picture', 'profilePicture');

        return $rsm;
    }

    /**
     * Get ads with 2 comments which have id in $ids
     *
     * @param Query $filterQuery
     * @param integer $since
     * @param integer $limit
     * @param string $order
     *
     * @return array
     */
    private function getAdsAndCommentsIn(Query $filterQuery, $since = null, $limit = 10, $order = null)
    {
        if ($since == null) {
            $since = 2000000000; // set to a very high last id if not present
        }
        $filterQuery->setParameter('since', $since)->setMaxResults($limit);
        $ids = array();
        foreach ($filterQuery->getArrayResult() as $ad) {
            $ids[] = $ad['id'];
        }

        return $this->_getAdsAndCommentsIn($ids, $order);
    }

    /**
     * The real implementation of getAdsAndCommentsIn
     *
     * @param array $ids
     */
    private function _getAdsAndCommentsIn($ids = array(), $order = null)
    {
        if (count($ids) <= 0) {
            return array();
        }
        $em = $this->getEntityManager();
        $rsm = $this->getNativeSqlMapping();
        $ids = Formatter::arrayToSql($ids);
        if (is_null($order)) {
            $order = 'a.id DESC';
        }
        $query = $em->createNativeQuery("
            SELECT a.*,
                u.id as u_id, u.username as u_username, u.imagename as u_imagename, u.real_name as u_real_name,
                u.profile_picture as u_profile_picture,
                c.id as c_id, c.content as c_content, c.created as c_created,
                cu.id as cu_id, cu.username as cu_username, cu.imagename as cu_imagename, cu.real_name as cu_real_name,
                cu.profile_picture as cu_profile_picture
            FROM ad a
            INNER JOIN users u ON a.user_id = u.id 
            LEFT JOIN ad_comment c ON c.ad_id = a.id
            LEFT JOIN users cu ON c.user_id = cu.id
            WHERE a.id IN $ids AND (
                a.comments_count = 0 OR (
                   a.comments_count > 0 AND c.id IN (
                       SELECT c0.id
                       FROM ad_comment c0
                       WHERE c0.ad_id = a.id
                       ORDER BY c0.id DESC
                       LIMIT 2
                   )
                )
            ) AND a.active = TRUE
            ORDER BY $order
        ", $rsm);

        return $query->getResult();
    }
}
