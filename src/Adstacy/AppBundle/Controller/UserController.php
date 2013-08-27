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
     * Show walls by $username
     *
     * @param string username
     */
    public function showWallsAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'walls';
        $params['walls'] = $this->getRepository('AdstacyAppBundle:Wall')->findByUser($params['user']);

        return $this->render('AdstacyAppBundle:User:show_walls.html.twig', $params);
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

    /**
     * Show promotes by $username
     *
     * @param string $username
     */
    public function showPromotesAction($username)
    {
        $params = $this->getParams($username);
        $params['tab'] = 'promotes';
        $query = $this->getRepository('AdstacyAppBundle:Ad')->findPromotesByUserQuery($params['user']);
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
            $params['wallsCount'] = $this->getRepository('AdstacyAppBundle:Wall')->countByUser($user);
            $params['promotesCount'] = $user->getPromotesCount();
            $params['followersCount'] = $user->getFollowersCount();
            $params['followingsCount'] = $user->getFollowingsCount();
        }

        return $params;
    }

    /**
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
                return new JsonResponse(json_encode(array('error' => 'You many not follow yourself')));
            }
            $this->addFlash('error', 'You may not follow yourself');
        } else {
            if ($loggedInUser->hasFollowUser($user)) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => 'You have already followed '.$username)));
                }

                $this->addFlash('error', 'You have already followed '.$username);
            } else {
                $user->addFollower($loggedInUser);
                foreach ($user->getWalls() as $wall) {
                    $wall->addFollower($loggedInUser);
                    $em->persist($wall);
                }
                $em->persist($user);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('username' => $username, 'followers_count' => $user->getFollowersCount())));
                }
                
                $this->addFlash('success', 'You have successfully followed '.$username); 
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $username)));
    }

    /**
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
                return new JsonResponse(json_encode(array('error' => 'You may not unfollow yourself')));
            }
            $this->addFlash('error', 'You may not unfollow yourself');
        } else {
            if (!$loggedInUser->hasFollowUser($user)) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => 'You have not followed '.$username)));
                }

                $this->addFlash('error', 'You have not followed '.$username);
            } else {
                $user->removeFollower($loggedInUser);
                foreach ($user->getWalls() as $wall) {
                    $wall->removeFollower($loggedInUser);
                    $em->persist($wall);
                }
                $em->persist($user);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('username' => $username, 'followers_count' => $user->getFollowersCount())));
                }
                
                $this->addFlash('success', 'You have successfully unfollowed '.$username); 
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $username)));
    }
}
