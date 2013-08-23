<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\WallRepository")
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
     * @Assert\Length(
     *    max = "50",
     *    maxMessage = "Wall name must not exceed {{ limit }} characters|Wall name must not exceed {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @Assert\Length(
     *    max = "255",
     *    maxMessage = "Wall description must not exceed {{ limit }} characters|Wall description must not exceed {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $tags;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Post", mappedBy="wall", cascade={"remove", "persist"})
     */
    private $posts;

    /**
     * @Exclude
     * @ORM\Column(name="posts_count", type="integer")
     */
    private $postsCount;

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="User", inversedBy="walls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Exclude
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followedWalls")
     */
    private $followers;

    /**
     * @Exclude
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
     * @param \Adstacy\AppBundle\Entity\Post $posts
     * @return Wall
     */
    public function addPost(\Adstacy\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
        $this->setPostsCount($this->getPostsCount() + 1);
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Adstacy\AppBundle\Entity\Post $posts
     */
    public function removePost(\Adstacy\AppBundle\Entity\Post $posts)
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
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return Wall
     */
    public function setUser(\Adstacy\AppBundle\Entity\User $user)
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

    /**
     * Add followers
     *
     * @param \Adstacy\AppBundle\Entity\User $followers
     * @return Wall
     */
    public function addFollower(\Adstacy\AppBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;
        $this->setFollowersCount($this->getFollowersCount() + 1);
    
        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Adstacy\AppBundle\Entity\User $followers
     */
    public function removeFollower(\Adstacy\AppBundle\Entity\User $followers)
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

    /**
     * Set description
     *
     * @param string $description
     * @return Wall
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $matches = null;
        preg_match_all('/#(\w+)/', $description, $matches);

        if ($this->getTags() != $matches[1]) { // only generate tags if tags are modified
            $this->generateTags();
        }
    
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
     * Generate tags from description
     */
    public function generateTags()
    {
        $matches = null;
        preg_match_all('/#(\w+)/', $this->description, $matches);
        $this->setTags($matches[1]);
        // TODO: unique tags
        foreach ($this->getPosts() as $post) {
            $post->setTags(array_merge($matches[1], $post->getTags()));
        }
    }

    /**
     * Set tags
     *
     * @param array $tags
     * @return Wall
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

    public function __toString()
    {
        return $this->name;
    }
}
