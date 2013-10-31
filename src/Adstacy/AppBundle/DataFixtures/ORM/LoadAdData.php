<?php

namespace Adstacy\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Entity\PromoteAd;
use Adstacy\AppBundle\Entity\Comment;
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
        $usernames = array('welly', 'suwandi', 'erwin', 'admin', 'andy', 'ricky', 'robert', 'wilson', 'dennis', 'hendra', 'david', 'rudy',
            'tony', 'jimmy', 'rita', 'fanny', 'dewi', 'kartika', 'angela', 'yenny', 'lisa', 'jenny',
            'erlika', 'tina', 'louis', 'sally', 'chistine', 'beny', 'yurica', 'melisa', 'anita',
            'wendy', 'susanti', 'albert', 'hendy', 'fendy', 'stanley', 'siska', 'cindy', 'catherine',
            'antonio', 'steven', 'novita', 'cynthia', 'andika', 'putra', 'putri', 'lusi', 'linda',
            'sanny', 'erna', 'halim', 'rani', 'dicky', 'july', 'donita', 'mega', 'mentari', 'mika'
        );
        $usernamesCnt = count($usernames);

        $ads = array();$imgIndex = 0;
        for ($i = 1; $i <= 48; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $description = $this->faker->sentence($this->faker->randomNumber(1, 50));
            $user = $this->getReference('user-'.$usernames[$this->faker->randomNumber(0, $usernamesCnt - 1)]);
            $ad = new Ad();
            if ($this->faker->randomNumber(0, 1) == 0 && $imgIndex < 32) {
                $image = $images[$imgIndex];
                $imgIndex++;
                $ad->setType('image');
                $ad->setImage($image);
            } else {
                $ad->setType('text');
                $ad->setTitle($this->faker->sentence($this->faker->randomNumber(1, 10)));
            }
            $nOfComments = $this->faker->randomNumber(0, 10);
            for ($k = 0; $k < $nOfComments; $k++) {
                $comment = new Comment();
                $comment->setContent($this->faker->sentence($this->faker->randomNumber(1, 15)));
                $commentUser = $this->getReference('user-'.$usernames[$this->faker->randomNumber(0, $usernamesCnt - 1)]);
                $comment->setUser($commentUser);
                $ad->addComment($comment);
            }
            $nOfPromotees = $this->faker->randomNumber(0, 25);
            for ($k = 0; $k < $nOfPromotees; $k++) {
                $promoteUser = $this->getReference('user-'.$usernames[$this->faker->randomNumber(0, $usernamesCnt - 1)]);
                if (!$promoteUser->hasPromote($ad)) {
                    $promote = new PromoteAd();
                    $promoteUser->addPromote($promote);
                    $ad->addPromotee($promote);
                }
            }

            $ad->setDescription($description);
            $ad->setTags($tags);
            $ad->setUser($user);
            $ad->setCreated($this->faker->dateTimeThisMonth);
            $ads[] = $ad;
            $manager->persist($ad);
            $this->addReference('ad-'.$i, $ad);
        }
        for ($i = 1; $i <= 15; $i++)
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
