<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{

    /**
     * Find $limit posts with id lower than $since
     *
     * @param integer|null $since
     * @param integer $limit
     *
     * @return array
     */
    public function findPostsSinceId($since, $limit = 30)
    {
        $em = $this->getEntityManager();
        $builder = $em->createQueryBuilder()
            ->select('p')
            ->from('AdstacyAppBundle:Post', 'p')
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
}
