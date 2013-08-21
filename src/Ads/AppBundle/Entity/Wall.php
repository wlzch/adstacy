<?php

namespace Ads\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Ads\AppBundle\Repository\WallRepository")
 */
class Wall
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Wall name must not be blank")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="wall", cascade={"remove"})
     */
    private $posts;

    /**
     * @ORM\Column(name="posts_count", type="integer")
     */
    private $postsCount;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="walls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followedWalls")
     */
    private $followers;

    /**
     * @ORM\Column(name="followers_count", type="integer")
     */
    private $followersCount;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPostsCount(0);
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setFollowersCount(0);
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
     * Set name
     *
     * @param string $name
     * @return Wall
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set followersCount
     *
     * @param integer $followersCount
     * @return Wall
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;
    
        return $this;
    }

    /**
     * Get followersCount
     *
     * @return integer 
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Add posts
     *
     * @param \Ads\AppBundle\Entity\Post $posts
     * @return Wall
     */
    public function addPost(\Ads\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
        $this->setPostsCount($this->getPostsCount() + 1);
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Ads\AppBundle\Entity\Post $posts
     */
    public function removePost(\Ads\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
        $this->setPostsCount($this->getPostsCount() - 1);
    
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set user
     *
     * @param \Ads\AppBundle\Entity\User $user
     * @return Wall
     */
    public function setUser(\Ads\AppBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Ads\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add followers
     *
     * @param \Ads\AppBundle\Entity\User $followers
     * @return Wall
     */
    public function addFollower(\Ads\AppBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;
        $this->setFollowersCount($this->getFollowersCount() + 1);
    
        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Ads\AppBundle\Entity\User $followers
     */
    public function removeFollower(\Ads\AppBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
        $this->setFollowersCount($this->getFollowersCount() - 1);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Set postsCount
     *
     * @param integer $postsCount
     * @return Wall
     */
    public function setPostsCount($postsCount)
    {
        $this->postsCount = $postsCount;
    
        return $this;
    }

    /**
     * Get postsCount
     *
     * @return integer 
     */
    public function getPostsCount()
    {
        return $this->postsCount;
    }
}
