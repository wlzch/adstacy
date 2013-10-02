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
        $q = $request->query->get('q');
        $tags = explode(' ', $q);

        $tagsFilter = new \Elastica\Filter\Terms('tags', $tags);

        $constantQuery = new \Elastica\Query\ConstantScore($tagsFilter);
        $constantQuery->setBoost(1);

        $existsFilter = new \Elastica\Filter\Exists('created');
        $finalQuery = new \Elastica\Query\CustomFiltersScore($constantQuery);
        foreach ($tags as $tag) {
            $termFilter = new \Elastica\Filter\Term(array('tags' => $tag));
            $finalQuery->addFilter($termFilter, 1.5);
        }
        $finalQuery->addFilterScript($existsFilter, 
            "(doc['created'].date.getMillis() / 1000000) * (1 / 10000000)"
        );
        $finalQuery->setScoreMode('total');

        $adsPaginator = $finder->findPaginated($finalQuery);
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
