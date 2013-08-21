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
     * @ORM\OneToOne(targetEntity="Image")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Wall", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wall;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="promotes")
     */
    private $promotees;

    /**
     * @ORM\Column(name="promotees_count", type="integer")
     */
    private $promotessCount;
}
