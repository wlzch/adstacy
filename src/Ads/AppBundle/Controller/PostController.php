<?php

namespace Ads\AppBundle\Controller;

use Ads\AppBundle\Entity\Post;
use Ads\AppBundle\Form\Type\PostType;
use Ads\AppBundle\Entity\Wall;
use Ads\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PostController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post, array(
            'username' => $this->getUser()->getUsername() 
        ));
        $form->handleRequest($this->getRequest());
        $wallForm = $this->createForm(new WallType(), new Wall(), array(
            'action' => $this->generateUrl('ads_app_wall_add') 
        ));

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->render('AdsAppBundle:Post:add.html.twig', array(
            'form' => $form->createView(),
            'wallForm' => $wallForm->createView()
        ));
    }
}
