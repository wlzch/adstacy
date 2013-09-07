<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\AdRepository")
 * @Vich\Uploadable
 */
class Ad
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
     *    minWidth = 236,
     *    mimeTypesMessage = "image.file.mime_types",
     *    minWidthMessage = "image.file.min_width"
     * )
     * @Vich\UploadableField(mapping="ad_image", fileNameProperty="imagename")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagename;

    /**
     * @Assert\Length(
     *  max = "255",
     *  maxMessage = "ad.description.max"
     * )
     * @ORM\COlumn(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="thumb_height", type="smallint", nullable=true)
     */
    private $thumbHeight;

    /**
     * @ORM\Column(name="image_width", type="smallint", nullable=true)
     */
    private $imageWidth;

    /**
     * @ORM\Column(name="image_height", type="smallint", nullable=true)
     */
    private $imageHeight;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ads", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="PromoteAd", mappedBy="ad", orphanRemoval=true, cascade={"persist","remove"})
     */
    private $promotees;

    /**
     * @ORM\Column(name="promotees_count", type="integer")
     */
    private $promoteesCount;

    /**
     * @ORM\OneToMany(targetEntity="FeaturedAd", mappedBy="ad")
     */
    private $featureds;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->promotees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->promoteesCount = 0;
        $this->created = new \Datetime();
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

    /**
     * Set description
     *
     * @param string $description
     * @return Ad
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $this->generateTags();
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Ad
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Generate tags from description
     */
    public function generateTags()
    {
        $matches = null;
        preg_match_all('/#(\w+)/', $this->description, $matches);
        // TODO: make tags unique
        $tags = $matches[1];
        $this->setTags($tags);
    }

    /**
     * Set tags
     *
     * @param array $tags
     * @return Ad
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    
        return $this;
    }

    /**
     * Get tags
     *
     * @return array 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set promoteesCount
     *
     * @param integer $promoteesCount
     * @return Ad
     */
    public function setPromoteesCount($promoteesCount)
    {
        $this->promoteesCount = $promoteesCount;
    
        return $this;
    }

    /**
     * Get promoteesCount
     *
     * @return integer 
     */
    public function getPromoteesCount()
    {
        return $this->promoteesCount;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Ad
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
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
     * Set thumbHeight
     *
     * @param integer $thumbHeight
     * @return Ad
     */
    public function setThumbHeight($thumbHeight)
    {
        $this->thumbHeight = $thumbHeight;
    
        return $this;
    }

    /**
     * Get thumbHeight
     *
     * @return integer 
     */
    public function getThumbHeight()
    {
        return $this->thumbHeight;
    }

    /**
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return Ad
     */
    public function setUser(\Adstacy\AppBundle\Entity\User $user)
    {
        $this->user = $user;
        $user->addAd($this);
    
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

    /**
     * Add featureds
     *
     * @param \Adstacy\AppBundle\Entity\FeaturedAd $featureds
     * @return Ad
     */
    public function addFeatured(\Adstacy\AppBundle\Entity\FeaturedAd $featureds)
    {
        $this->featureds[] = $featureds;
    
        return $this;
    }

    /**
     * Remove featureds
     *
     * @param \Adstacy\AppBundle\Entity\FeaturedAd $featureds
     */
    public function removeFeatured(\Adstacy\AppBundle\Entity\FeaturedAd $featureds)
    {
        $this->featureds->removeElement($featureds);
    }

    /**
     * Get featureds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeatureds()
    {
        return $this->featureds;
    }

    public function setImage(File $image = null)
    {
        if ($image && $this->image != $image) {
            $this->image = $image;
            $size = getimagesize($image);
            if ($size[0] > 0 && $size[1] > 0) {
                $this->setImageWidth($size[0]);
                $this->setImageHeight($size[1]);
            }
            // hack for VichUploaderBundle because the listener will be called 
            // only if there is any field changes
            $this->setUpdated(new \Datetime());
        }
    
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
     * @return Ad
     */
    public function setImagename($imagename)
    {
        if ($imagename) {
            $this->imagename = $imagename;
        }
    
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

    public function __toString()
    {
        return $this->description;
    }


    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Ad
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add promotees
     *
     * @param \Adstacy\AppBundle\Entity\PromoteAd $promotees
     * @return Ad
     */
    public function addPromotee(\Adstacy\AppBundle\Entity\PromoteAd $promotees)
    {
        $this->promotees[] = $promotees;
        $this->setPromoteesCount($this->getPromoteesCount() + 1);
        $promotees->setAd($this);
    
        return $this;
    }

    /**
     * Remove promotees
     *
     * @param \Adstacy\AppBundle\Entity\PromoteAd $promotees
     */
    public function removePromotee(\Adstacy\AppBundle\Entity\PromoteAd $promotees)
    {
        $this->promotees->removeElement($promotees);
        $this->setPromoteesCount($this->getPromoteesCount() - 1);
    }

    /**
     * Get promotees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPromotees()
    {
        return $this->promotees;
    }

    /**
     * Set imageWidth
     *
     * @param integer $imageWidth
     * @return Ad
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = $imageWidth;
    
        return $this;
    }

    /**
     * Get imageWidth
     *
     * @return integer 
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Set imageHeight
     *
     * @param integer $imageHeight
     * @return Ad
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = $imageHeight;
    
        return $this;
    }

    /**
     * Get imageHeight
     *
     * @param integer|null
     *
     * @return integer 
     */
    public function getImageHeight($width = null)
    {
        if ($this->imageHeight) {
            if ($width) {
                return round(($width / $this->getImageWidth()) * $this->imageHeight);
            }

            return $this->imageHeight;
        }

        return $this->getThumbHeight();
    }
}
