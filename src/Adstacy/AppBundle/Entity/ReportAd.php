<?php

namespace Adstacy\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\ReportAdRepository")
 * @ORM\Table(name="report_ad")
 */
class ReportAd
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ad")
     */
    private $ad;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\Column(name="reported_at", type="datetime")
     */
    private $reportedAt;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $status;

    public function __construct()
    {
        $this->reportedAt = new \DateTime();
        $this->status = 'new';
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
     * Set ad
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ad
     * @return ReportAd
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
     * Set user
     *
     * @param \Adstacy\AppBundle\Entity\User $user
     * @return ReportAd
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

    /**
     * Set reportedAt
     *
     * @param \DateTime $reportedAt
     * @return ReportAd
     */
    public function setReportedAt($reportedAt)
    {
        $this->reportedAt = $reportedAt;
    
        return $this;
    }

    /**
     * Get reportedAt
     *
     * @return \DateTime 
     */
    public function getReportedAt()
    {
        return $this->reportedAt;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ReportAd
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
