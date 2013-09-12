<?php

namespace Adstacy\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Adstacy\NotificationBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Adstacy\AppBundle\Entity\User")
     */
    private $from;

    /**
     * @ORM\ManyToOne(targetEntity="Adstacy\AppBundle\Entity\User", inversedBy="notifications")
     */
    private $to;

    /**
     * @ORM\ManyToOne(targetEntity="Adstacy\AppBundle\Entity\Comment")
     * @ORM\JoinColumn(name="comment", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Adstacy\AppBundle\Entity\Ad")
     * @ORM\JoinColumn(name="ad", nullable=true)
     */
    private $ad;

    /**
     * @ORM\Column(type="boolean")
     */
    private $read;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->read = false;
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
     * Set type
     *
     * @param string $type
     * @return Notification
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
     * Set read
     *
     * @param boolean $read
     * @return Notification
     */
    public function setRead($read)
    {
        $this->read = $read;
    
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Notification
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
     * Set from
     *
     * @param \Adstacy\AppBundle\Entity\User $from
     * @return Notification
     */
    public function setFrom(\Adstacy\AppBundle\Entity\User $from = null)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return \Adstacy\AppBundle\Entity\User 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \Adstacy\AppBundle\Entity\User $to
     * @return Notification
     */
    public function setTo(\Adstacy\AppBundle\Entity\User $to = null)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return \Adstacy\AppBundle\Entity\User 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set comment
     *
     * @param \Adstacy\AppBundle\Entity\Comment $comment
     * @return Notification
     */
    public function setComment(\Adstacy\AppBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return \Adstacy\AppBundle\Entity\Comment 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ad
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ad
     * @return Notification
     */
    public function setAd(\Adstacy\AppBundle\Entity\Ad $ad = null)
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
}