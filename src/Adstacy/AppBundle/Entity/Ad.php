<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\AdRepository")
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
     * @Assert\Valid
     * @Assert\NotNull(message="You must upload an image")
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"}, fetch="EAGER")
     */
    private $image;

    /**
     * @Assert\Length(
     *  max = "255",
     *  maxMessage = "Ad description at most {{ limit }} characters"
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ads", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="promotes")
     * @ORM\JoinTable(name="promotes_ad",
     *    joinColumns={
     *      @ORM\JoinColumn(name="ad_id", referencedColumnName="id")
     *    },
     *    inverseJoinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *    }
     * )
     */
    private $promotees;

    /**
     * @ORM\Column(name="promotees_count", type="integer")
     */
    private $promoteesCount;

    /**
     * @ORM\OneToOne(targetEntity="FeaturedAd", mappedBy="ad")
     */
    private $featured;

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
     * Set image
     *
     * @param \Adstacy\AppBundle\Entity\Image $image
     * @return Ad
     */
    public function setImage(\Adstacy\AppBundle\Entity\Image $image = null)
    {
        if ($image && $this->image != $image) {
            $this->image = $image;
            $size = getimagesize($image->getFile());
            if ($size[0] > 0) {
                $height = round((236 / $size[0]) * $size[1]);
                $this->setThumbHeight($height);
            }
        }
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Adstacy\AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add promotees
     *
     * @param \Adstacy\AppBundle\Entity\User $promotees
     * @return Ad
     */
    public function addPromotee(\Adstacy\AppBundle\Entity\User $promotees)
    {
        $this->promotees[] = $promotees;
        $this->setPromoteesCount($this->getPromoteesCount() + 1);
        $promotees->addPromote($this);
    
        return $this;
    }

    /**
     * Remove promotees
     *
     * @param \Adstacy\AppBundle\Entity\User $promotees
     */
    public function removePromotee(\Adstacy\AppBundle\Entity\User $promotees)
    {
        $this->promotees->removeElement($promotees);
        $this->setPromoteesCount($this->getPromoteesCount() - 1);
        $promotees->removePromote($this);
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
     * Set featured
     *
     * @param \Adstacy\AppBundle\Entity\Image $featured
     * @return Ad
     */
    public function setFeatured(\Adstacy\AppBundle\Entity\Image $featured = null)
    {
        $this->featured = $featured;
    
        return $this;
    }

    /**
     * Get featured
     *
     * @return \Adstacy\AppBundle\Entity\Image 
     */
    public function getFeatured()
    {
        return $this->featured;
    }
}
