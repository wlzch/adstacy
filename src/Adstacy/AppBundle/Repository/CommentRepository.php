<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Adstacy\AppBundle\Entity\Ad;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
    public function findByAd(Ad $ad)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT c,
            partial u.{id,username,imagename,realName}
            FROM AdstacyAppBundle:Comment c
            JOIN c.ad a
            JOIN c.user u
            WHERE a.id = :id
        ');

        return $query->setParameter('id', $ad->getId())->getResult();
    }
}
