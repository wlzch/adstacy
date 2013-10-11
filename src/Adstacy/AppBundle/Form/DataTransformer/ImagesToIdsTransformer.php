<?php

namespace Adstacy\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\TempAdImage;
use Adstacy\AppBundle\Entity\AdImage;

class ImagesToIdsTransformer implements DataTransformerInterface
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transform $images to imageids text
     *
     * @param array $images
     *
     * @return string $ids (separated by comma)
     */
    public function transform($images = array())
    {
        $ids = array();
        if ($images) {
            foreach ($images as $image) {
                $ids[] = $image->getId();
            }
        }

        return implode(',', $ids);
    }

    /**
     * Transform images' id to $images
     *
     * @param string images' id (separated by comma)
     *
     * @return array $images
     */
    public function reverseTransform($ids)
    {
        $parts = explode(';', $ids);
        $imageIds = array();
        $tempIds = array();
        $imageIds = array_filter(explode(',', $parts[0]), 'is_numeric');
        $images = array();
        if (count($parts) == 2) {
            $tempIds = array_filter(explode(',', $parts[1]), 'is_numeric');
        }
        if (count($imageIds) > 0) {
            $images = $this->om->getRepository('AdstacyAppBundle:AdImage')->findById($imageIds);
        }
        if (count($tempIds) > 0) {
            $tempImages = $this->om->getRepository('AdstacyAppBundle:TempAdImage')->findById($tempIds);
            foreach ($tempImages as $image) {
                $adImage = new AdImage();
                $adImage->setImage($image->getImage());
                $adImage->setImagename($image->getImagename());
                $images[] = $adImage;
            }
        }

        return $images;
    }
}
