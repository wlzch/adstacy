<?php

namespace Adstacy\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\Serializer\SerializationContext;

class AdController extends ApiController
{
    /**
     * Return ad information
     *
     * @param integer ad id
     */
    public function showAction($id)
    {
        if (($ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id)) == false) {
            throw $this->createNotFoundException();
        }

        $res = array(
            'data' => array(
                'ad' => $ad
            ),
            'meta' => array(
            )
        );

        $serializer = $this->get('serializer');

        return new Response($serializer->serialize($res, 'json', SerializationContext::create()->setGroups(array('ad_show'))));
    }

    /**
     * Return comments
     *
     * @param integer ad $id
     */
    public function listCommentsAction($id)
    {
        if (($ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id)) == false) {
            throw $this->createNotFoundException();
        }

        $until = $this->getRequest()->query->get('until');
        $limit = $this->getParameter('max_comments_per_page');
        $comments = $this->getRepository('AdstacyAppBundle:Comment')->findByAd($ad, $until, $limit);

        if (count($comments) <= 0) {
            return new JsonResponse(array('data' => array()));
        }

        $next = $comments[count($comments)-1]->getId();
        $res = array(
            'data' => array(
                'comments' => array_reverse($comments)
            ),
            'meta' => array(
                'prev' => $this->generateUrl('adstacy_api_ad_comments', array('id' => $id, 'until' => $next))
            )
        );

        $serializer = $this->get('serializer');

        return new Response($serializer->serialize($res, 'json', SerializationContext::create()->setGroups(array('comment_list'))));
    }
}
