<?php

namespace Adstacy\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\Serializer\SerializationContext;
use Adstacy\AppBundle\Helper\Formatter;

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
        if ($paginator->hasNextPage()) {
            $next = $paginator->getNextPage();
            $res['meta']['next'] = $this->generateUrl('adstacy_api_ad_broadcasts', array('id' => $id, 'page' => $next));
        }

        return $this->response($res, 'user_list');
    }

    /**
     * Randomly chose trending ad
     */
    public function listTrendingAction()
    {
        $redis = $this->get('snc_redis.default');
        $cacheManager = $this->get('liip_imagine.cache.manager');
        $thumbWidth = $this->getParameter('small_thumb_size');
        $em = $this->getManager();
        $max = $this->getParameter('max_sidebar_trending');

        $cnt = $redis->zcard('trending');
        $rand = rand(0, $cnt - $max - 1);
        $ids = $redis->zrevrange('trending', $rand, $rand + $max - 1);

        $ads = array();
        $formattedIds = Formatter::arrayToSql($ids);
        $query = $em->createQuery("
          SELECT partial a.{id, type, imagename, imageWidth, imageHeight, created, title, youtubeUrl}
          FROM AdstacyAppBundle:Ad a
          WHERE a.id IN $formattedIds AND a.active = TRUE
        ");
        foreach ($query->getResult() as $ad) {
            $_ad = array(
              'id' => $ad->getId(),
              'created' => $ad->getCreated()
            );
            if ($ad->getType() == 'image') {
                $_ad['is_image'] = true;
                $_ad['image'] = $cacheManager->getBrowserPath($ad->getImagename(), 'small_thumb');
                $_ad['width'] = $thumbWidth;
                $_ad['height'] = $ad->getImageHeight($thumbWidth);
                if ($_ad['height'] <= 0) continue;
            } else if ($ad->getType() == 'text') {
                $_ad['is_text'] = true;
                $_ad['title'] = $ad->getTitle();
            } else if ($ad->getType() == 'youtube') {
                $_ad['is_youtube'] = true;
                $_ad['youtube_id'] = $ad->getYoutubeId();
            }
            $ads[] = $_ad;
        }
        $res = array(
            'data' => array(
                'ads' => $ads
            ),
            'meta' => array()
        );

        return $this->response($res, 'ad_list');
    }
}
