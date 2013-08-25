<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\WallRepository")
 * @UniqueEntity(fields={"name", "user"}, errorPath="name", message="Wall name must be unique")
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
     * @ORM\OneToMany(targetEntity="Ad", mappedBy="wall", cascade={"remove", "persist"})
     */
    private $ads;

    /**
     * @Exclude
     * @ORM\Column(name="ads_count", type="integer")
     */
    private $adsCount;

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="User", inversedBy="walls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Exclude
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followedWalls")
     * @ORM\JoinTable(name="followed_walls",
     *    joinColumns={
     *      @ORM\JoinColumn(name="wall_id", referencedColumnName="id")
     *    },
     *    inverseJoinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *    }
     * )
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
        $this->ads = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setAdsCount(0);
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
     * Add ads
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ads
     * @return Wall
     */
    public function addAd(\Adstacy\AppBundle\Entity\Ad $ads)
    {
        $this->ads[] = $ads;
        $this->setAdsCount($this->getAdsCount() + 1);
    
        return $this;
    }

    /**
     * Remove ads
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ads
     */
    public function removeAd(\Adstacy\AppBundle\Entity\Ad $ads)
    {
        $this->ads->removeElement($ads);
        $this->setAdsCount($this->getAdsCount() - 1);
    
    }

    /**
     * Get ads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAds()
    {
        return $this->ads;
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
     * Set adsCount
     *
     * @param integer $adsCount
     * @return Wall
     */
    public function setAdsCount($adsCount)
    {
        $this->adsCount = $adsCount;
    
        return $this;
    }

    /**
     * Get adsCount
     *
     * @return integer 
     */
    public function getAdsCount()
    {
        return $this->adsCount;
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
        foreach ($this->getAds() as $ad) {
            $ad->setTags(array_merge($matches[1], $ad->getTags()));
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
