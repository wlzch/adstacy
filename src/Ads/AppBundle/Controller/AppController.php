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

    public function searchAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.post');

        $postsPaginator = $finder->findPaginated($request->query->get('q'));
        $postsPaginator
            ->setMaxPerPage(20)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        return $this->render('AdsAppBundle:App:search.html.twig', array(
            'postsPaginator' => $postsPaginator
        ));
    }
}
