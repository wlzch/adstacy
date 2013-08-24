<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class AppController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $maxAd = $this->getParameter('max_ad_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findAdsSinceId($request->query->get('id'), $maxAd);
        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Includes:ads.html.twig', array(
                'ads' => $ads
            ));
        }

        return $this->render('AdstacyAppBundle:App:index.html.twig', array(
            'ads' => $ads
        ));
    }

    public function searchAction()
    {
        $request = $this->getRequest();
        $finder = $this->get('fos_elastica.finder.website.ad');

        $adsPaginator = $finder->findPaginated($request->query->get('q'));
        $adsPaginator
            ->setMaxPerPage(20)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;

        if ($request->isXmlHttpRequest()) {
            return $this->render('AdstacyAppBundle:Includes:ads_search.html.twig', array(
                'paginator' => $adsPaginator
            ));
        }

        return $this->render('AdstacyAppBundle:App:search.html.twig', array(
            'paginator' => $adsPaginator
        ));
    }
}
