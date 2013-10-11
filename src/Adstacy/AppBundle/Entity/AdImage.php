<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Adstacy\AppBundle\Helper\ImageHelper;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\AdImageRepository")
 * @ORM\Table(name="ad_image")
 * @Vich\Uploadable
 */
class AdImage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Image(
     *    maxSize = "5M",
     *    mimeTypes = {"image/png", "image/jpg", "image/jpeg", "image/pjpeg"},
     *    minWidth = 320,
     *    maxSizeMessage = "image.file.max_size",
     *    mimeTypesMessage = "image.file.mime_types",
     *    minWidthMessage = "image.file.min_width"
     * )
     * @Assert\NotNull(message="image.file.not_null")
     * @Vich\UploadableField(mapping="ad_image", fileNameProperty="imagename")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagename;

    /**
     * @ORM\ManyToOne(targetEntity="Ad", inversedBy="images")
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setImage(File $image)
    {
        $this->image = ImageHelper::resizeImage($image, 768);
    
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imagename
     *
     * @param string $imagename
     * @return TempAdImage
     */
    public function setImagename($imagename)
    {
        $this->imagename = $imagename;
    
        return $this;
    }

    /**
     * Get imagename
     *
     * @return string 
     */
    public function getImagename()
    {
        return $this->imagename;
    }

    /**
     * Get created
     * 
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Image
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Set ad
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ad
     * @return Image
     */
    public function setAd(\Adstacy\AppBundle\Entity\Ad $ad = null)
    {
        $this->ad = $ad;
    
        return $this;
    }

    /**
     * Get ad
     *
     * @return \Adstacy\AppBundle\Entity\Ad 
     */
    public function getAd()
    {
        return $this->ad;
    }
}
