<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\PromoteAdRepository")
 * @ORM\Table(name="promotes_ad")
 */
class PromoteAd
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ad", inversedBy="promotees")
     */
    private $ad;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="promotes")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \Datetime();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PromoteAd
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
     * Set ad
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ad
     * @return PromoteAd
     */
    public function setAd(\Adstacy\AppBundle\Entity\Ad $ad)
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

    /**
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return PromoteAd
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
}
