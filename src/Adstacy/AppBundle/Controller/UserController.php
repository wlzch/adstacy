<?php

namespace Adstacy\AppBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;

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
        $params['ads'] = $this->getRepository('AdstacyAppBundle:Ad')->findByUser($params['user']);

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
        $params['promotes'] = $this->getRepository('AdstacyAppBundle:Ad')->findPromotesByUser($params['user']);

        return $this->render('AdstacyAppBundle:User:show_promotes.html.twig', $params);
    }

    /**
     * Show followers by $username
     *
     * @param string $username
     */
    public function showFollowersAction($username)
    {
        $params = $this->getParams($username);
        $params['followers'] = $this->getRepository('AdstacyAppBundle:User')->findFollowersByUser($params['user']);

        return $this->render('AdstacyAppBundle:User:show_followers.html.twig', $params);
    }

    /**
     * Show followings by $username
     *
     * @param string $username
     */
    public function showFollowingsAction($username)
    {
        $params = $this->getParams($username);
        $params['followings'] = $this->getRepository('AdstacyAppBundle:User')->findFollowingsByUser($params['user']);

        return $this->render('AdstacyAppBundle:User:show_followings.html.twig', $params);
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
        $adsCount = $this->getRepository('AdstacyAppBundle:Ad')->countByUser($user);
        $wallsCount = $this->getRepository('AdstacyAppBundle:Wall')->countByUser($user);
        $promotesCount = $this->getRepository('AdstacyAppBundle:Ad')->countPromotesByUser($user);
        $followersCount = $this->getRepository('AdstacyAppBundle:User')->countFollowersByUser($user);
        $followingsCount = $this->getRepository('AdstacyAppBundle:User')->countFollowingsByUser($user);

        return array(
            'user' => $user,
            'adsCount' => $adsCount,
            'wallsCount' => $wallsCount,
            'promotesCount' => $promotesCount,
            'followersCount' => $followersCount,
            'followingsCount' => $followingsCount
        );
    }
}
