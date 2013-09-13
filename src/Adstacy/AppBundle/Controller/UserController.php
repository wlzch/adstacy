<?php

namespace Adstacy\AppBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function showAction($username)
    {
        return $this->showAdsAction($username);
    }

    /**
     * Show ads by $username
     *
     * @param string $username
     */
    public function showAdsAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'ads';
        $query = $this->getRepository('AdstacyAppBundle:Ad')->findByUserQuery($params['user']);
        $params['paginator'] = $this->getDoctrinePaginator($query, $this->getParameter('max_ads_per_page'));
        $params['route'] = 'adstacy_app_user_ads';

        return $this->render('AdstacyAppBundle:User:show_ads.html.twig', $params);
    }

    public function showPromotesAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'promotes';
        $query = $this->getRepository('AdstacyAppBundle:Ad')->findByPromoteQuery($params['user']);
        $params['paginator'] = $this->getDoctrinePaginator($query, $this->getParameter('max_ads_per_page'));
        $params['route'] = 'adstacy_app_user_promotes';

        return $this->render('AdstacyAppBundle:User:show_ads.html.twig', $params);
    }

    /**
     * Show followers by $username
     *
     * @param string $username
     */
    public function showFollowersAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'followers';
        $query = $this->getRepository('AdstacyAppBundle:User')->findFollowersByUserQuery($params['user']);
        $params['paginator'] = $this->getDoctrinePaginator($query, $this->getParameter('max_users_per_page'));
        $params['route'] = 'adstacy_app_user_followers';

        return $this->render('AdstacyAppBundle:User:show_users.html.twig', $params);
    }

    /**
     * Show followings by $username
     *
     * @param string $username
     */
    public function showFollowingsAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'followings';
        $query = $this->getRepository('AdstacyAppBundle:User')->findFollowingsByUserQuery($params['user']);
        $params['paginator'] = $this->getDoctrinePaginator($query, $this->getParameter('max_users_per_page'));
        $params['route'] = 'adstacy_app_user_followings';

        return $this->render('AdstacyAppBundle:User:show_users.html.twig', $params);
    }

    /**
     * Get parameters to render in twig
     *
     * @param string $username
     *
     * @return array
     */
    private function getParams($username)
    {
        $user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $params = array('user' => $user);
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $params['adsCount'] = $user->getAdsCount();
            $params['promotesCount'] = $user->getPromotesCount();
            $params['followersCount'] = $user->getFollowersCount();
            $params['followingsCount'] = $user->getFollowingsCount();
        }

        return $params;
    }

    /**
     * @Secure(roles="ROLE_USER")
     * Follow User with $username
     *
     * @param string $username
     */
    public function followAction($username)
    {
        $request = $this->getRequest();
        $em = $this->getManager();
        $user = $em->getRepository('AdstacyAppBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $loggedInUser = $this->getUser();
        if ($loggedInUser == $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => $this->translate('flash.user.follow.error_self'))));
            }
            $this->addFlash('error', $this->translate('flash.user.follow.error_self'));
        } else {
            if ($loggedInUser->hasFollowUser($user)) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => $this->translate('flash.user.follow.error_followed', array('%username%' => $username)))));
                }

                $this->addFlash('error', $this->translate('flash.user.follow.error_followed', array('%username%' => $username)));
            } else {
                $user->addFollower($loggedInUser);
                $this->get('adstacy.notification.manager')->save($loggedInUser, $user, null, false, 'follow');
                $em->persist($user);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('username' => $username, 'followers_count' => $user->getFollowersCount())));
                }

                $this->addFlash('success', $this->translate('flash.user.follow.success', array('%username%' => $username)));
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $username)));
    }

    /**
     * @Secure(roles="ROLE_USER")
     * Unfollow User with $username
     *
     * @param string $username
     */
    public function unfollowAction($username)
    {
        $request = $this->getRequest();
        $em = $this->getManager();
        $user = $em->getRepository('AdstacyAppBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $loggedInUser = $this->getUser();
        if ($loggedInUser == $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => $this->translate('flash.user.unfollow.error_self'))));
            }
            $this->addFlash('error', $this->translate('flash.user.unfollow.error_self'));
        } else {
            if (!$loggedInUser->hasFollowUser($user)) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => $this->translate('flash.user.unfollow.error_not_followed', array('%username%' => $username)))));
                }

                $this->addFlash('error', $this->translate('flash.error.unfollow.error_not_followed', array('%username%' => $username)));
            } else {
                $user->removeFollower($loggedInUser);
                $em->persist($user);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('username' => $username, 'followers_count' => $user->getFollowersCount())));
                }

                $this->addFlash('success', $this->translate('flash.user.unfollow.success', array('%username%' => $username)));
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $username)));
    }
}
