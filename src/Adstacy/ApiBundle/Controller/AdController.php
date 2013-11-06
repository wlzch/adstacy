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

        return $this->response($res, 'ad_show');
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
            return $this->noResult();
        }

        $next = $comments[0]->getId();
        $res = array(
            'data' => array(
                'comments' => $comments
            ),
            'meta' => array(
                'prev' => $this->generateUrl('adstacy_api_ad_comments', array('id' => $id, 'until' => $next))
            )
        );

        return $this->response($res, 'comment_list');
    }

    /**
     * Return promotes
     *
     * @param integer $id
     */
    public function listBroadcastsAction($id)
    {
        if (($ad = $this->getRepository('AdstacyAppBundle:Ad')->find($id)) == false) {
            throw $this->createNotFoundException();
        }
        $repo = $this->getRepository('AdstacyAppBundle:User');
        $query = $repo->findPromotesAdQuery($ad);
        $limit = $this->getParameter('max_list_broadcasts_per_page');
        $paginator = $this->getDoctrinePaginator($query, $limit);
        if ($paginator->getNbResults() <= 0) {
            return $this->noResult();
        }
        $result = $paginator->getCurrentPageResults()->getArrayCopy();
        $res = array(
            'data' => array(
                'broadcasts' => $result
            ),
            'meta' => array()
        );
        if (count($result) >= $limit) {
            $next = ($this->getRequest()->query->get('page') ?: 1) + 1;
            $res['meta']['next'] = $this->generateUrl('adstacy_api_ad_broadcasts', array('id' => $id, 'page' => $next));
        }

        return $this->response($res, 'user_list');
    }
}
