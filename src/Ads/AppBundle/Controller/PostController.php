<?php

namespace Ads\AppBundle\Controller;

use Ads\AppBundle\Entity\Post;
use Ads\AppBundle\Form\Type\PostType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PostController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->render('AdsAppBundle:Post:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
