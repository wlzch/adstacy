<?php

namespace Ads\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Ads\AppBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Image")
     */
    private $image;

    /**
     * @Assert\Length(
     *  max = "255",
     *  maxMessage = "Post description at most {{ limit }} characters"
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
     * @ORM\ManyToOne(targetEntity="Wall", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $wall;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="promotes")
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
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $matches = null;
        preg_match_all('/#(\w+)/', $description, $matches);
        $this->setTags($matches[1]);
    
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
     * @return Post
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
     * Set tags
     *
     * @param array $tags
     * @return Post
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
     * @return Post
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
     * @param \Ads\AppBundle\Entity\Image $image
     * @return Post
     */
    public function setImage(\Ads\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Ads\AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set wall
     *
     * @param \Ads\AppBundle\Entity\Wall $wall
     * @return Post
     */
    public function setWall(\Ads\AppBundle\Entity\Wall $wall)
    {
        $this->wall = $wall;
    
        return $this;
    }

    /**
     * Get wall
     *
     * @return \Ads\AppBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * Add promotees
     *
     * @param \Ads\AppBundle\Entity\User $promotees
     * @return Post
     */
    public function addPromotee(\Ads\AppBundle\Entity\User $promotees)
    {
        $this->promotees[] = $promotees;
        $this->setPromoteesCount($this->getPromoteesCount() + 1);
    
        return $this;
    }

    /**
     * Remove promotees
     *
     * @param \Ads\AppBundle\Entity\User $promotees
     */
    public function removePromotee(\Ads\AppBundle\Entity\User $promotees)
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
}
