<?php

namespace Ads\AppBundle\Entity;

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
     *    minHeight = 160,
     *    mimeTypesMessage = "Image yang diupload harus berupa png atau jpg",
     *    minWidthMessage = "Lebar image yang diupload terlalu kecil ({{ width }}px). Minimal lebar yang diperbolehkan adalah {{ min_width }}px",
     *    minHeightMessage = "Panjang image yang diupload terlalu kecil ({{ height }}px). Minimal panjang yang diperbolehkan adalah {{ min_height }}px"
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
