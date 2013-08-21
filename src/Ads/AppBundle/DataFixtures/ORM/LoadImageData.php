<?php

namespace Ads\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;
use Ads\AppBundle\Entity\Image;

class LoadImageData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();

        $path = __DIR__.'/../img';
        foreach ($finder->files()->in($path) as $img) {
            $image = new Image();
            $image->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            $manager->persist($image);

            $this->setReference('image-'.$img->getBasename('.jpg'), $image);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
