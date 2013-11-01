<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Adstacy\AppBundle\Helper\ImageHelper;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\TempAdImageRepository")
 * @ORM\Table(name="temp_ad_image")
 * @Vich\Uploadable
 */
class TempAdImage
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
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

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
        $this->image = ImageHelper::resizeImage($image, 640);
    
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
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return TempAdImage
     */
    public function setUser(\Adstacy\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Adstacy\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
