<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Entity\PromoteAd;
use Adstacy\AppBundle\Form\Type\AdType;
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
        $adsByUser = $this->getRepository('AdstacyAppBundle:Ad')->findByUser($ad->getUser(), 6);

        return $this->render('AdstacyAppBundle:Ad:show.html.twig', array(
            'ad' => $ad,
            'adsByUser' => $adsByUser
        ));
    }
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function createAction()
    {
        $user = $this->getUser();
        $request = $this->getRequest();
        $ad = new Ad();
        $form = $this->createForm(new AdType(), $ad);
        $form->handleRequest($request);
        $ad->setUser($user);

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', $this->translate('flash.ad.create.success'));

            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }

        return $this->render('AdstacyAppBundle:Ad:form.html.twig', array(
            'form' => $form->createView()
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

        if ($ad->getUser() != $user) {
            $this->addFlash('error', $this->translate('flash.ad.edit.error_diff_user'));
            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }
        $form = $this->createForm(new AdType(), $ad);
        $image = $ad->getImage(); //temporary hack because form set the image to null if image is not valid
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', $this->translate('flash.ad.edit.success'));

            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }
        $ad->setImage($image);

        return $this->render('AdstacyAppBundle:Ad:form.html.twig', array(
            'form' => $form->createView()
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
            $this->addFlash('error', $this->translate('flash.ad.delete.error_diff_user'));
            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }
        $user->removeAd($ad);
        
        $em = $this->getManager();
        $em->remove($ad);
        $em->flush();
        $this->addFlash('success', $this->translate('flash.ad.delete.success'));

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
            $promote = new PromoteAd();
            $ad->addPromotee($promote);
            $user->addPromote($promote);
            $em = $this->getManager();
            $em->persist($ad);
            $em->persist($user);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $ad->getId(), 'promotes_count' => $ad->getPromoteesCount())));
            }
            $this->addFlash('success', $this->translate('flash.ad.promote.success'));
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => $this->translate('flash.ad.promote.error_twice'))));
            }
            $this->addFlash('error', $this->translate('flash.ad.promote.error_twice'));
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
            $promoteAd = $this->getRepository('AdstacyAppBundle:PromoteAd')->findOneBy(array('user' => $user, 'ad' => $ad));
            $ad->removePromotee($promoteAd);
            $user->removePromote($promoteAd);
            $em = $this->getManager();
            $em->persist($ad);
            $em->persist($user);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('id' => $ad->getId(), 'promotes_count' => $ad->getPromoteesCount())));
            }
            $this->addFlash('success', 'You successfully unpromoted this ad');
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => $this->translate('flash.ad.unpromote.error_not_promote'))));
            }
            $this->addFlash('error', $this->translate('flash.ad.unpromote.error_not_promote'));
        }

        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $id)));
    }
}
