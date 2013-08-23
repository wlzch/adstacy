<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Post;
use Adstacy\AppBundle\Form\Type\PostType;
use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PostController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $filter = 'thumbnail';
        $post = new Post();
        $form = $this->createForm(new PostType(), $post, array(
            'username' => $this->getUser()->getUsername() 
        ));
        $form->handleRequest($request);
        $wallForm = $this->createForm(new WallType(), new Wall(), array(
            'action' => $this->generateUrl('adstacy_app_wall_add') 
        ));

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->render('AdstacyAppBundle:Post:add.html.twig', array(
            'form' => $form->createView(),
            'wallForm' => $wallForm->createView()
        ));
    }
}
