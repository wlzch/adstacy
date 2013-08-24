<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Post;
use Adstacy\AppBundle\Form\Type\PostType;
use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer post id
     */
    public function promoteAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $post = $this->getRepository('AdstacyAppBundle:Post')->find($id);

        // user can only promote once
        if (!$user->hasPromote($post)) {
            $post->addPromotee($this->getUser());
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $post->getId(), 'promotes_count' => $post->getPromoteesCount())));
            }
            $this->addFlash('success', 'You successfully promoted this post');
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You have not promote this post yet')));
            }
            $this->addFlash('error', 'You may not promote this post twice');
        }

        return $this->redirect($this->generateUrl('adstacy_app_post_show', array('id' => $id)));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer post id
     */
    public function unpromoteAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $post = $this->getRepository('AdstacyAppBundle:Post')->find($id);

        // user can only unpromote if he has promote
        if ($user->hasPromote($post)) {
            $post->removePromotee($this->getUser());
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $post->getId(), 'promotes_count' => $post->getPromoteesCount())));
            }
            $this->addFlash('success', 'You successfully unpromoted this post');
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You have not promote this post yet')));
            }
            $this->addFlash('error', 'You have not promote this post yet');
        }

        return $this->redirect($this->generateUrl('adstacy_app_post_show', array('id' => $id)));
    }
}
