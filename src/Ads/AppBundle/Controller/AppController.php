<?php

namespace Ads\AppBundle\Controller;

class AppController extends Controller
{
    public function indexAction()
    {
        $posts = $this->getRepository('AdsAppBundle:Post')->findAll();

        return $this->render('AdsAppBundle:App:index.html.twig', array(
            'posts' => $posts
        ));
    }
}
