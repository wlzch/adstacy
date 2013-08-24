<?php

namespace Adstacy\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Adstacy\AppBundle\Entity\User;

/**
 * WallRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WallRepository extends EntityRepository
{

    /**
     * Find walls by user with its corresponding ads count
     *
     * @param User $user
     *
     * @return array array(0 => Wall, 'adsCount' => integer)
     */
    public function findByUser(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT w, COUNT(a.id) as adsCount
            FROM AdstacyAppBundle:Wall w
            JOIN w.user u
            JOIN w.ads a
            WHERE u.id = :id
            GROUP BY w.id
        ');

        return $query->setParameter('id', $user->getId())->getResult();
    }
}
