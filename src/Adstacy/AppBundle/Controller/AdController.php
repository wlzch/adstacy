<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Entity\Comment;
use Adstacy\AppBundle\Entity\PromoteAd;
use Adstacy\AppBundle\Entity\ReportAd;
use Adstacy\AppBundle\Entity\TempAdImage;
use Adstacy\AppBundle\Form\Type\AdType;
use Adstacy\AppBundle\Form\Type\TempAdImageType;
use Adstacy\AppBundle\Form\Type\CommentType;
use Adstacy\AppBundle\Helper\ImageHelper;
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
        $securityContext = $this->get('security.context');
        if (!$ad || ($ad && $ad->getUser() !== $this->getUser() && $ad->getActive() == false) && !$securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createNotFoundException();
        }
        $adsByUser = $this->getRepository('AdstacyAppBundle:Ad')->findByUser($ad->getUser(), 4);
        $form = $this->createForm(new CommentType(), new Comment(), array(
            'action' => $this->generateUrl('adstacy_app_ad_comment', array('id' => $id))
        ));
        $comments = $this->getRepository('AdstacyAppBundle:Comment')->findByAd($ad, null, 8);

        return $this->render('AdstacyAppBundle:Ad:show.html.twig', array(
            'ad' => $ad,
            'adsByUser' => $adsByUser,
            'form' => $form->createView(),
            'comments' => $comments
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

        if ($request->isMethod('POST') && $ad->getImagename()) {
            $image = $this->getRepository('AdstacyAppBundle:TempAdImage')->findOneByImagename($ad->getImagename());
            if ($image) {
                if ($image->getUser() == $this->getUser()) {
                    $ad->setImage($image->getImage());
                } else {
                    $ad->setImage(null);
                }
            }
        }
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

        if ($ad->getUser() !== $user) {
            $this->addFlash('error', $this->translate('flash.ad.edit.error_diff_user'));
            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
        }

        $form = $this->createForm(new AdType(), $ad);
        $image = $ad->getImage(); //temporary hack because form set the image to null if image is not valid
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $ad->getImagename()) {
            $tempimage = $this->getRepository('AdstacyAppBundle:TempAdImage')->findOneByImagename($ad->getImagename());
            if ($tempimage) {
                if ($tempimage->getUser() == $this->getUser()) {
                    $ad->setImage($tempimage->getImage());
                } else {
                    $ad->setImage(null);
                }
            }
        }

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
        $request = $this->getRequest();
        $adUser = $ad->getUser();
        $user = $this->getUser();
        if ($adUser !== $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('status' => 'error', 'message' => $this->translate('flash.ad.delete.error_diff_user')));
            } else {
                $this->addFlash('error', $this->translate('flash.ad.delete.error_diff_user'));
                return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $ad->getId())));
            }
        }
        $user->removeAd($ad);
        
        $em = $this->getManager();
        $em->remove($ad);
        $em->flush();
        $redis = $this->get('snc_redis.default');
        $redis->lrem('trending', 1, $id);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('status' => 'ok', 'message' => $this->translate('flash.ad.delete.success')));
        }
        $this->addFlash('success', $this->translate('flash.ad.delete.success'));

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $user->getUsername())));
    }

    /**
     * @param integer ad id
     */
    public function showPromotesAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }

        $limit = $this->getParameter('max_users_per_page');
        $query = $this->getRepository('AdstacyAppBundle:User')->findPromotesByAd($ad);
        $paginator = $this->getDoctrinePaginator($query, $limit);

        return $this->render('AdstacyAppBundle:Ad:show_promotes.html.twig', array(
            'paginator' => $paginator
        ));
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
        if (!$ad) {
            throw $this->createNotFoundException();
        }

        // user can only promote once
        if (!$user->hasPromote($ad)) {
            $promote = new PromoteAd();
            // ordering matters
            $user->addPromote($promote);
            $ad->addPromotee($promote);
            $em = $this->getManager();
            $this->get('adstacy.notification.manager')->save($user, $ad->getUser(), $ad, false, 'promote');
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
        if (!$ad) {
            throw $this->createNotFoundException();
        }

        // user can only unpromote if he has promote
        if ($user->hasPromote($ad)) {
            $promoteAd = $this->getRepository('AdstacyAppBundle:PromoteAd')->findOneBy(array('user' => $user, 'ad' => $ad));
            // ordering matters
            $user->removePromote($promoteAd);
            $ad->removePromotee($promoteAd);
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

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer ad id
     */
    public function commentAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getManager();
            $ad->addComment($comment);
            $comment->setUser($user);

            $adUser = $ad->getUser();
            $notificationManager = $this->get('adstacy.notification.manager');
            $notificationManager->save($user, $adUser, $comment, false);

            $em->persist($ad);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('status' => 'ok', 'data' => $comment));
            }
            $this->addFlash('comment.success', $this->translate('ads.comment.success'));
        } else {
            $errors = array();
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array('status' => 'error', 'errors' => $errors));
            }
            foreach ($errors as $error) {
                $this->addFlash('comment.warning', $error);
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $id)).'#comments');
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer comment id
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->getRepository('AdstacyAppBundle:Comment')->find($id);
        if (!$comment) {
            throw $this->createNotFoundException();
        }
        $user = $this->getUser();
        $request = $this->getRequest();
        $ad = $comment->getAd();
        if ($user !== $comment->getUser() && $user !== $ad->getUser()) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('status' => 'error', 'message' => $this->translate('ads.comment.delete.fail'))));
            }
            $this->addFlash('error', $this->translate('ads.comment.delete.fail'));

            return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $comment->getAd()->getId())));
        }

        $em = $this->getManager();
        $ad->removeComment($comment);
        $em->remove($comment);
        $em->persist($ad);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(json_encode(array('status' => 'ok', 'message' => $this->translate('ads.comment.delete.success'))));
        }

        $this->addFlash('success', $this->translate('ads.comment.delete.success'));

        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $comment->getAd()->getId())));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * @param integer ad id
     */
    public function reportAction($id)
    {
        $ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id);
        if (!$ad) {
            throw $this->createNotFoundException();
        }
        $report = new ReportAd();
        $report->setAd($ad);
        $report->setUser($this->getUser());
        $em = $this->getManager();
        $em->persist($report);
        $em->flush();

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse(array('status' => 'ok', 'message' => $this->translate('ads.report.success')));
        }

        $this->addFlash('success', $this->translate('ads.report.success'));
        return $this->redirect($this->generateUrl('adstacy_app_ad_show', array('id' => $id)));
    }

    /*
     * Upload image
     *
     * @return JsonResponse
     */
    public function uploadImageAction()
    {
        $request = $this->getRequest();
        $image = new TempAdImage();
        $form = $this->createForm(new TempAdImageType(), $image);
        $request->query->remove('ad');
        $request->request->remove('ad');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getManager();
            $image->setUser($this->getUser());
            $em->persist($image);
            $em->flush();

            $assetHelper = $this->get('adstacy.helper.asset');
            $uploaderHelper = $this->get('vich_uploader.templating.helper.uploader_helper');

            return new JsonResponse(array(
                'status' => 'ok',
                'files' => array(
                    array (
                        'name' => $image->getImagename(),
                        'src' => $assetHelper->assetUrl($uploaderHelper->asset($image, 'image')),
                        'id' => $image->getId()
                    )
                )
            ));
        }
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(array('status' => 'error', 'errors' => $errors));
    }

    /**
     * Upload image with url
     *
     * @Secure(roles="ROLE_USER")
     */
    public function uploadImageFromUrlAction()
    {
        $request = $this->getRequest();
        $url = $request->query->get('url');
        if (!$url) {
            return new JsonResponse(array('status' => 'error', 'message' => $this->translate('upload_image.url_required')));
        }
        $file = ImageHelper::downloadImage($url);

        if ($file === false) {
            return new JsonResponse(array('status' => 'error', 'message' => $this->translate('upload_image.url_not_valid')));
        }

        $image = new TempAdImage();
        $image->setImage($file);
        $image->setImagename($file->getFilename());
        $image->setUser($this->getUser());

        $validator = $this->get('validator');
        $errors = $validator->validate($image);
        if (count($errors) > 0) {
            $_errors = array();
            foreach ($errors as $error) {
                $_errors[] = $error->getMessage();
            }
            return new JsonResponse(array('status' => 'error', 'errors' => $_errors));
        }

        $em = $this->getManager();
        $em->persist($image);
        $em->flush();

        $assetHelper = $this->get('adstacy.helper.asset');
        $uploaderHelper = $this->get('vich_uploader.templating.helper.uploader_helper');
        return new JsonResponse(array(
            'status' => 'ok',
            'files' => array(
                array (
                    'name' => $image->getImagename(),
                    'src' => $assetHelper->assetUrl($uploaderHelper->asset($image, 'image')),
                    'id' => $image->getId()
                )
            )
        ));
    }
}
