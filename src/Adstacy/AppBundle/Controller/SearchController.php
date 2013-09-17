<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class SearchController extends Controller
{
    public function searchAction()
    {
        if ($this->getRequest()->query->get('type') == 'users') {
            return $this->searchUsersAction();
        }
        return $this->searchAdsAction();
    }

    public function searchAdsAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.ad');
        $limit = $this->getParameter('max_ads_per_page');
        if ($this->isMobile()) $limit = $limit / 2;

        $adsPaginator = $finder->findPaginated($request->query->get('q'));
        $adsPaginator
            ->setMaxPerPage($limit)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        return $this->render('AdstacyAppBundle:Search:ads.html.twig', array(
            'paginator' => $adsPaginator,
            'search' => 'ads'
        ));
    }

    public function searchUsersAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.user');
        $q = $request->query->get('q');
        $limit = $this->getParameter('max_users_per_page');
        if ($this->isMobile()) $limit = $limit / 2;

        $usersPaginator = $finder->findPaginated($request->query->get('q'));
        $usersPaginator 
            ->setMaxPerPage($limit)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        return $this->render('AdstacyAppBundle:Search:users.html.twig', array(
            'paginator' => $usersPaginator,
            'search' => 'users'
        ));
    }
}
