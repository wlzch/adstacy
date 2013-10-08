<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\UserDetailRepository")
 * @ORM\Table(name="user_detail")
 */
class UserDetail
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User", inversedBy="detail")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="simple_array", name="facebook_friends", nullable=true)
     */
    private $facebookFriends;

    /**
     * @ORM\Column(type="simple_array", name="twitter_friends", nullable=true)
     */
    private $twitterFriends;

    /**
     * @ORM\Column(type="simple_array", name="twitter_followers", nullable=true)
     */
    private $twitterFollowers;

    /**
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return UserDetail
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
     * Set facebookFriends
     *
     * @param array $facebookFriends
     * @return UserDetail
     */
    public function setFacebookFriends($facebookFriends = array())
    {
        $this->facebookFriends = $facebookFriends;
    
        return $this;
    }

    /**
     * Get facebookFriends
     *
     * @return array 
     */
    public function getFacebookFriends()
    {
        return $this->facebookFriends;
    }

    /**
     * Set twitterFriends
     *
     * @param array $twitterFriends
     * @return UserDetail
     */
    public function setTwitterFriends($twitterFriends = array())
    {
        $this->twitterFriends = $twitterFriends;
    
        return $this;
    }

    /**
     * Get twitterFriends
     *
     * @return array 
     */
    public function getTwitterFriends()
    {
        return $this->twitterFriends;
    }

    /**
     * Set twitterFollowers
     *
     * @param array $twitterFollowers
     * @return UserDetail
     */
    public function setTwitterFollowers($twitterFollowers)
    {
        $this->twitterFollowers = $twitterFollowers;
    
        return $this;
    }

    /**
     * Get twitterFollowers
     *
     * @return array 
     */
    public function getTwitterFollowers()
    {
        return $this->twitterFollowers;
    }
}