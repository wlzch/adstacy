<?php

namespace Adstacy\AppBundle\Controller;

class PageController extends Controller
{
    /**
     * Show page
     *
     * @param string $key
     */
    public function showAction($key)
    {
        $page = $this->getRepository('AdstacyAppBundle:Page')->findOneByKey($key);
        if (!$page) {
            throw $this->createNotFoundException();
        }

        return $this->render('AdstacyAppBundle:Page:show.html.twig', array(
            'page' => $page
        ));
    }
}
