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
     * @ORM\OneToMany(targetEntity="Post", mappedBy="wall")
     */
    private $posts;

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
}
