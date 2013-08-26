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
     * @ORM\ManyToOne(targetEntity="Wall", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $wall;

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
        if ($wall = $this->getWall()) {
            $tags = array_merge($tags, $wall->getTags());
        }
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
        if ($image) {
            $this->image = $image;
            $size = getimagesize($image->getFile());
            $height = round((236 / $size[0]) * $size[1]);
            $this->setThumbHeight($height);
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
     * Set wall
     *
     * @param \Adstacy\AppBundle\Entity\Wall $wall
     * @return Ad
     */
    public function setWall(\Adstacy\AppBundle\Entity\Wall $wall)
    {
        $this->wall = $wall;
        $this->generateTags();
    
        return $this;
    }

    /**
     * Get wall
     *
     * @return \Adstacy\AppBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
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
}