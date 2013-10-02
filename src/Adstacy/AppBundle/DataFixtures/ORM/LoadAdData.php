<?php

namespace Adstacy\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\Ad;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;

class LoadAdData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $path = __DIR__.'/../img';
        $images = array();
        foreach ($finder->files()->in($path) as $img) {
            $images[] = new UploadedFile($img->getRealPath(), $img->getFilename());
        }

        $ads = array();
        for ($i = 1; $i <= 32; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $definedTags = array('promo', 'jual');
            $tags = array_merge($tags, $definedTags);
            $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            $image = $images[$i - 1];
            $users = array($this->getReference('user-suwandi'), $this->getReference('user-welly'), $this->getReference('user-erwin'));
            $user = $users[$this->faker->randomNumber(0, count($users) - 1)];

            $ad = new Ad();
            $ad->setImage($image);
            $ad->setDescription($description);
            $ad->setUser($user);
            if ($this->faker->randomNumber(1, 5) % 2 == 0) {
                $ad->setContent($this->faker->paragraph($this->faker->randomNumber(4, 30)));
            }
            $ad->setCreated($this->faker->dateTimeThisMonth);
            $ads[] = $ad;
            $manager->persist($ad);
            $this->addReference('ad-'.$i, $ad);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
