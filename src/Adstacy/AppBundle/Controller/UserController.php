<?php

namespace Adstacy\AppBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;

class UserController extends Controller
{
    public function showAction($username)
    {
        $user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $walls = $this->getRepository('AdstacyAppBundle:Wall')->findByUser($user);
        $adsCount = $this->getRepository('AdstacyAppBundle:Ad')->countByUser($user);

        return $this->render('AdstacyAppBundle:User:show.html.twig', array(
            'user' => $user,
            'walls' => $walls,
            'adsCount' => $adsCount
        ));
    }
}
