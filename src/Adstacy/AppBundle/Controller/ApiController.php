<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class ApiController extends Controller
{
    /**
     * Get users
     * Use redis to store cache.
     */
    public function usersAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em = $this->getManager();
            $query = $em->createQuery('
                SELECT u.realName as name, u.username
                FROM AdstacyAppBundle:User u
            ');
            $query->useResultCache(true, 3600, 'FindAllUsersForAutocomplete');
            $results = $query->getScalarResult();

            $response = new JsonResponse(json_encode($results));
            $response->setMaxAge(3600);
            $response->setSharedMaxAge(3600);

            return $response;
        }

        return new Response("Don't access this url directly");
    }
}
