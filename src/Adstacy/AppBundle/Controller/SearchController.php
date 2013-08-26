<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class SearchController extends Controller
{
    public function searchAction()
    {
        return $this->searchAdsAction();
    }

    public function searchAdsAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.ad');

        $adsPaginator = $finder->findPaginated($request->query->get('q'));
        $adsPaginator
            ->setMaxPerPage(20)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Search:ads_content.html.twig', array(
                'paginator' => $adsPaginator
            ));
        }

        return $this->render('AdstacyAppBundle:Search:ads.html.twig', array(
            'paginator' => $adsPaginator,
            'search' => 'ads'
        ));
    }

    public function searchUsersAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.user');

        $usersPaginator = $finder->findPaginated($request->query->get('q'));
        $usersPaginator 
            ->setMaxPerPage(20)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Search:users_content.html.twig', array(
                'paginator' => $usersPaginator
            ));
        }

        return $this->render('AdstacyAppBundle:Search:users.html.twig', array(
            'paginator' => $usersPaginator,
            'search' => 'users'
        ));
    }
}
