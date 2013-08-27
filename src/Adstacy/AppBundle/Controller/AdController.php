<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Form\Type\AdType;
use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdController extends Controller
{

    /**
     * Show single ad
     */
    public function showAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }

        return $this->render('AdstacyAppBundle:Ad:show.html.twig', array(
            'ad' => $ad
        ));
    }
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function createAction()
    {
        $user = $this->getUser();
        $request = $this->getRequest();
        $filter = 'thumbnail';
        $ad = new Ad();
        $form = $this->createForm(new AdType(), $ad, array(
            'username' => $user->getUsername() 
        ));
        $form->handleRequest($request);
        $wallForm = $this->createForm(new WallType(), new Wall(), array(
            'action' => $this->generateUrl('adstacy_app_wall_create') 
        ));

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', 'You have successfully created your ads');

            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }

        return $this->render('AdstacyAppBundle:Ad:form.html.twig', array(
            'form' => $form->createView(),
            'wallForm' => $wallForm->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        $request = $this->getRequest();
        $filter = 'thumbnail';

        if ($ad->getUser() != $user) {
            $this->addFlash('error', 'You can only edit your own ads');
            return $this->redirect($this->generateUrl('homepage'));
        }
        $form = $this->createForm(new AdType(), $ad, array(
            'username' => $user->getUsername() 
        ));
        $image = $ad->getImage(); //temporary hack because form set the image to null if image is not valid
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', 'You have successfully edited your ads');

            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }
        $wallForm = $this->createForm(new WallType(), new Wall(), array(
            'action' => $this->generateUrl('adstacy_app_wall_create') 
        ));
        $ad->setImage($image);

        return $this->render('AdstacyAppBundle:Ad:form.html.twig', array(
            'form' => $form->createView(),
            'wallForm' => $wallForm->createView()
        ));
    }

    /**
     * Delete
     *
     * @param integer $id
     */
    public function deleteAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }
        $adUser = $ad->getUser();
        $user = $this->getUser();
        if ($adUser != $user) {
            $this->addFlash('error', 'You can only delete your own ads');
            return $this->redirect($this->generateUrl('homepage'));
        }
        $user->removeAd($ad);
        
        $em = $this->getManager();
        $em->remove($ad);
        $em->flush();
        $this->addFlash('success', 'You have successfully deleted your ads');

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $user->getUsername())));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer ad id
     */
    public function promoteAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);

        // user can only promote once
        if (!$user->hasPromote($ad)) {
            $ad->addPromotee($user);
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $ad->getId(), 'promotes_count' => $ad->getPromoteesCount())));
            }
            $this->addFlash('success', 'You successfully promoted this ad');
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You have not promote this ad yet')));
            }
            $this->addFlash('error', 'You may not promote this ad twice');
        }

        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $id)));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer ad id
     */
    public function unpromoteAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);

        // user can only unpromote if he has promote
        if ($user->hasPromote($ad)) {
            $ad->removePromotee($user);
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $ad->getId(), 'promotes_count' => $ad->getPromoteesCount())));
            }
            $this->addFlash('success', 'You successfully unpromoted this ad');
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You have not promote this ad yet')));
            }
            $this->addFlash('error', 'You have not promote this ad yet');
        }

        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $id)));
    }
}
