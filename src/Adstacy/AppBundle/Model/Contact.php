<?php

namespace Adstacy\AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    private $subject;

    /**
     * @Assert\NotBlank(message="Email must not be blank")
     * @Assert\Email(message="Email is not valid")
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Content must not be blank")
     */
    private $content;

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setEmail($email)
    {
        $this->email= $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setContent($content)
    {
        $this->content= $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
