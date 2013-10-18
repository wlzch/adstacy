<?php

namespace Adstacy\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Adstacy\AppBundle\Controller\Controller;

class ApiController extends Controller
{
    /**
     * Return $res as JsonResponse.
     * $res will be serialized to json with jms_serializer
     *
     * @param mixed $res the response to be serialized
     * @param array|string $groups serializer groups
     *
     * @return JsonResponse
     */
    public function response($res, $groups)
    {
        if (is_string($groups)) {
            $groups = array($groups);
        }
        $serializer = $this->get('serializer');
        return new JsonResponse(
            $serializer->serialize($res, 'json', 
                count($groups) > 0 ? SerializationContext::create()->setGroups($groups) : null
            )
        );
    }

    /**
     * Return json response with no result
     *
     * @return JsonResponse
     */
    public function noResult()
    {
        return new JsonResponse(array('data' => array()));
    }
}
