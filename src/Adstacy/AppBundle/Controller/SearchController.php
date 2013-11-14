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
        $index = $this->get('fos_elastica.index.website.ad');
        $limit = $this->getParameter('max_ads_per_page');
        $q = strtolower($request->query->get('q'));
        $tags = explode(' ', $q);
        $tags = preg_replace('/[^A-Za-z0-9]/', '', $tags);

        $tagsFilter = new \Elastica\Filter\Terms('normalizedTags', $tags);

        $constantQuery = new \Elastica\Query\ConstantScore($tagsFilter);
        $constantQuery->setBoost(1);

        $existsFilter = new \Elastica\Filter\Exists('created');
        $filterQuery = new \Elastica\Query\CustomFiltersScore($constantQuery);
        foreach ($tags as $tag) {
            $termFilter = new \Elastica\Filter\Term(array('normalizedTags' => $tag));
            $filterQuery->addFilter($termFilter, 1.5);
        }
        $filterQuery->addFilterScript($existsFilter, 
            "(doc['created'].date.getMillis() / 1000000) * (1 / 10000000)"
        );
        $filterQuery->setScoreMode('total');

        $activeFilter = new \Elastica\Filter\Term(array('active' => true));
        $filterQuery->addFilter($activeFilter, 1.5);

        $finalQuery = new \Elastica\Query($filterQuery);

        $page = $request->query->get('page') ?: 1;
        $from = ($page - 1) * $limit;
        $finalQuery->setFrom($from)->setLimit($limit)->setFields(array('id'));
        $resultSet = $index->search($finalQuery);
        $ids = array();
        foreach ($resultSet->getResults() as $result) {
            $ids[] = $result->getId();
        }
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findByIds($ids);

        return $this->render('AdstacyAppBundle:Search:ads.html.twig', array(
            'ads' => $ads,
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
        $redis = $this->get('snc_redis.default');
        if ($redis->hexists("user:$username", 'id')) {
            return $this->redirect($this->generateUrl('adstacy_app_user_profile', array('username' => $username)));
        };
        //$limit = $this->getParameter('max_users_per_page');

        //$usersPaginator = $finder->findPaginated($username);
        //$usersPaginator 
            //->setMaxPerPage($limit)
            //->setCurrentPage($request->query->get('page') ?: 1)
        //;

        return $this->render('AdstacyAppBundle:Search:users.html.twig', array(
            'users' => array(),
            'search' => 'users'
        ));
    }

}
