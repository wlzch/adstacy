<?php

namespace Adstacy\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Adstacy\AppBundle\Entity\Image;

class ImageToUploadedFileTransformer implements DataTransformerInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transform uploaded file to image
     *
     * @param Image
     *
     * @return String
     */
    public function transform($image)
    {
        if (null === $image) {
            return null;
        }

        return $image->getFile();
    }

    /**
     * Transform image to uploaded file
     *
     * @param Image
     *
     * @return UpladedFile
     */
    public function reverseTransform($file)
    {
        if (null === $file) {
            return null;
        }
        $image = new Image();
        $image->setFile($file);

        return $image;
    }
}
