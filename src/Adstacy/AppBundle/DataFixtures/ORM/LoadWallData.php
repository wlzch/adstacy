<?php

namespace Adstacy\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\Wall;

class LoadWallData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 15; $i++) {
            $name = $this->faker->sentence($this->faker->randomNumber(1, 3));
            $users = array($this->getReference('user-suwandi'), $this->getReference('user-welly'), $this->getReference('user-erwin'));
            $user = $users[$this->faker->randomNumber(0, count($users) - 1)];

            $description = null;
            if ($this->faker->randomNumber(1, 10) <= 8) {
                $tags = $this->faker->words($this->faker->randomNumber(0, 8));
                $definedTags = array('promo', 'jual');
                $tags = array_merge($tags, $definedTags);
                $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            }

            $wall = new Wall();
            $wall->setName($name);
            $wall->setUser($user);
            $wall->setDescription($description);

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
