<?php

namespace Adstacy\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\Ad;

class LoadAdData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $ads = array();
        for ($i = 1; $i <= 32; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $definedTags = array('promo', 'jual');
            $tags = array_merge($tags, $definedTags);
            $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            $image = $this->getReference('image-'.$i);
            $wall = $this->getReference('wall-'.$this->faker->randomNumber(1, 15));
            $user = $wall->getUser();

            $ad = new Ad();
            $ad->setImage($image);
            $ad->setDescription($description);
            $ad->setWall($wall);
            if ($this->faker->randomNumber(1, 5) % 2 == 0) {
                $ad->setContent($this->faker->paragraph($this->faker->randomNumber(4, 30)));
            }
            $ads[] = $ad;
            $user->increaseAdsCount();
            $manager->persist($ad);
            $manager->persist($user);
            $this->addReference('ad-'.$i, $ad);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
