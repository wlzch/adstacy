<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class AppController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $maxPost = $this->getParameter('max_post_per_page');
        $posts = $this->getRepository('AdstacyAppBundle:Post')->findPostsSinceId($request->query->get('id'), $maxPost);
        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Includes:posts.html.twig', array(
                'posts' => $posts
            ));
        }

        return $this->render('AdstacyAppBundle:App:index.html.twig', array(
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

        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Includes:posts_search.html.twig', array(
                'paginator' => $postsPaginator
            ));
        }

        return $this->render('AdstacyAppBundle:App:search.html.twig', array(
            'paginator' => $postsPaginator
        ));
    }
}
