<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Imagine\Gd\Imagine;
use Imagine\Filter\Advanced\RelativeResize;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\AdRepository")
 * @Assert\Callback(methods={"isAdValid"})
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("none")
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $type;

    /**
     * @Vich\UploadableField(mapping="ad_image", fileNameProperty="imagename")
     * @JMS\Exclude
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Exclude
     */
    private $imagename;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $youtubeId;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $description;

    /**
     * @Assert\Count(
     *  min="1",
     *  minMessage= "ad.tags.min_count"
     * )
     * @ORM\Column(type="simple_array", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $created;

    /**
     * @ORM\Column(name="thumb_height", type="smallint", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $thumbHeight;

    /**
     * @ORM\Column(name="image_width", type="smallint", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $imageWidth;

    /**
     * @ORM\Column(name="image_height", type="smallint", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $imageHeight;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ads", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="PromoteAd", mappedBy="ad", orphanRemoval=true, cascade={"persist","remove"})
     * @JMS\Exclude
     */
    private $promotees;

    /**
     * @ORM\Column(name="promotees_count", type="integer")
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $promoteesCount;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="ad", orphanRemoval=true, cascade={"persist", "remove"})
     * @JMS\Groups({"ad_list"})
     */
    private $comments;

    /**
     * @ORM\Column(name="comments_count", type="integer", nullable=true)
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $commentsCount;

    /**
     * @Assert\Count(
     *  max="5",
     *  maxMessage = "ad.images.max_count"
     * )
     * @ORM\OneToMany(targetEntity="AdImage", mappedBy="ad", orphanRemoval=true, cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="FeaturedAd", mappedBy="ad")
     * @JMS\Exclude
     */
    private $featureds;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"user_show", "ad_list"})
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Exclude
     */
    protected $updated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->promotees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->promoteesCount = 0;
        $this->commentsCount = 0;
        $this->created = new \Datetime();
        $this->updated = new \Datetime();
        $this->active = true;
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
     * Set tags
     *
     * @param array $tags
     * @return Ad
     */
    public function setTags($tags = array())
    {
        $this->tags = preg_replace('/[^A-Za-z0-9 ]/', '', $tags);
    
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
        if ($image && $this->image !== $image) { 
            $originalImage = $image;
            $imagine = new Imagine();
            $image = $imagine->open($image);
            $size = $image->getSize();
            if ($size->getWidth() > 0 && $size->getHeight() > 0) {
                if ($size->getWidth() > 1024) {
                    $relativeResize = new RelativeResize('widen', 1024);
                    $image = $relativeResize->apply($image);
                    $image->save($originalImage->getRealPath(), array('format' => $originalImage->guessClientExtension()));
                    $size = $image->getSize();
                }
                $this->image = $originalImage;
                $this->setImageWidth($size->getWidth());
                $this->setImageHeight($size->getHeight());
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

    /**
     * Set commentsCount
     *
     * @param integer $commentsCount
     * @return Ad
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    
        return $this;
    }

    /**
     * Get commentsCount
     *
     * @return integer 
     */
    public function getCommentsCount()
    {
        return $this->commentsCount ?: 0;
    }

    /**
     * Add comments
     *
     * @param \Adstacy\AppBundle\Entity\Comment $comments
     * @return Ad
     */
    public function addComment(\Adstacy\AppBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
        $this->setCommentsCount($this->getCommentsCount() + 1);
        $comments->setAd($this);
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Adstacy\AppBundle\Entity\Comment $comments
     */
    public function removeComment(\Adstacy\AppBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
        $this->setCommentsCount($this->getCommentsCount() - 1);
        $comments->setAd(null);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function isAdValid(ExecutionContextInterface $context)
    {
        if (!$this->type) {
            $context->addViolationAt('type', 'ad.type.not_blank');
        } else {
            if ($this->type == 'image') {
                if (!$this->imagename && !$this->image) {
                    $context->addViolationAt('image', 'image.file.not_null');
                } else {
                    $context->validateValue($this->image, new Assert\Image(array(
                        'maxSize' => '5M',
                        'mimeTypes' => array('image/png', 'image/jpg', 'image/jpeg', 'image/pjpeg'),
                        'minWidth' => 320,
                        'maxSizeMessage' => 'image.file.max_size',
                        'mimeTypesMessage' => 'image.file.mime_types',
                        'minWidthMessage' => 'image.file.min_width'
                    )));
                }
            } else if ($this->type == 'text') {
                if (!$this->title) {
                    $context->addViolationAt('title', 'ad.title.not_blank');
                }
                if (!$this->description) {
                    $context->addViolationAt('description', 'ad.description.not_blank');
                }
            } else if ($this->type == 'youtube') {
                if (!$this->youtubeId) {
                    $context->addViolationAt('youtubeId', 'ad.youtubeId.not_blank');
                }
            } else {
                $context->addViolation('ad.type.not_supported');
            }
        }
    }


    public function __toString()
    {
        return $this->description;
    }


    /**
     * Set active
     *
     * @param boolean $active
     * @return Ad
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add images
     *
     * @param \Adstacy\AppBundle\Entity\AdImage $images
     * @return Ad
     */
    public function addImage(\Adstacy\AppBundle\Entity\AdImage $images)
    {
        $this->images[] = $images;
        $images->setAd($this);
    
        return $this;
    }

    /**
     * Remove images
     *
     * @param \Adstacy\AppBundle\Entity\AdImage $images
     */
    public function removeImage(\Adstacy\AppBundle\Entity\AdImage $images)
    {
        $this->images->removeElement($images);
        $images->setAd(null);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Ad
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Ad
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set youtubeId
     *
     * @param string $youtubeId
     * @return Ad
     */
    public function setYoutubeId($youtubeId)
    {
        $this->youtubeId = $youtubeId;
    
        return $this;
    }

    /**
     * Get youtubeId
     *
     * @return string 
     */
    public function getYoutubeId()
    {
        return $this->youtubeId;
    }
}
