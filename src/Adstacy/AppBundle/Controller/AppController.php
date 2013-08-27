<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

class AppController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $maxAd = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findAdsSinceId($request->query->get('id'), $maxAd);

        return $this->render('AdstacyAppBundle:App:index.html.twig', array(
            'ads' => $ads
        ));
    }
}
