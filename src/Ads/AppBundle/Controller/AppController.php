<?php

namespace Ads\AppBundle\Controller;

class AppController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdsAppBundle:App:index.html.twig');
    }
}
