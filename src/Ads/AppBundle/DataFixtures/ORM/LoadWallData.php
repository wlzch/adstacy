<?php

namespace Ads\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Ads\AppBundle\Entity\Wall;

class LoadWallData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 15; $i++) {
            $name = $this->faker->sentence($this->faker->randomNumber(1, 3));
            $users = array($this->getReference('user-suwandi'), $this->getReference('user-welly'), $this->getReference('user-erwin'));
            $user = $users[$this->faker->randomNumber(0, count($users) - 1)];

            $wall = new Wall();
            $wall->setName($name);
            $wall->setUser($user);

            $manager->persist($wall);
            $this->addReference('wall-'.$i, $wall);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
