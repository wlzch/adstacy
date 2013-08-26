<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Image(
     *    maxSize = "1M",
     *    mimeTypes = {"image/png", "image/jpeg", "image/pjpeg"},
     *    minWidth = 240,
     *    mimeTypesMessage = "Image must be type of png or jpg",
     *    minWidthMessage = "Image width is too small({{ width }}px). Min width allowed is {{ min_width }}px"
     * )
     * @Vich\UploadableField(mapping="image", fileNameProperty="filename")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setFile($file)
    {
        if ($file) {
            $this->file = $file;
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Image
     */
    public function setFilename($filename)
    {
        if ($filename) {
            $this->filename = $filename;
        }
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
