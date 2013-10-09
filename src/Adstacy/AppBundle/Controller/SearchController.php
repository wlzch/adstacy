<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class SearchController extends Controller
{
    public function searchAction()
    {
        $q = $this->getRequest()->query->get('q');
        if ($q && strlen($q) > 1 && $q[0] == '@') {
            return $this->searchUsers(substr($q, 1));
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
        $tags = preg_replace('/[^A-Za-z0-9]/', '', $tags);

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

        $activeFilter = new \Elastica\Filter\Term(array('active' => true));
        $finalQuery->addFilter($activeFilter, 1.5);

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

        return $this->searchUsers($request->query->get('q'));
    }

    private function searchUsers($username)
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.user');
        $limit = $this->getParameter('max_users_per_page');
        if ($this->isMobile()) $limit = $limit / 2;

        $usersPaginator = $finder->findPaginated($username);
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
