<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\CommentRepository")
 * @ORM\Table(name="ad_comment")
 * @JMS\ExclusionPolicy("none")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"ad_list"})
     */
    private $id;

    /**
     * @Assert\NotBlank(message="comment.content.not_blank")
     * @Assert\Length(
     *  max = "255",
     *  maxMessage = "comment.content.max"
     * )
     * @ORM\Column(type="text", length=255)
     * @JMS\Groups({"ad_list"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"ad_list"})
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @JMS\Groups({"ad_list"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ad", inversedBy="comments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @JMS\Exclude
     */
    private $ad;

    public function __construct()
    {
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
     * Set content
     *
     * @param string $content
     * @return Comment
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
     * Set ad
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ad
     * @return Comment
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

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Comment
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
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Adstacy\AppBundle\Entity\User $user = null)
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
