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
        $results = array();
        $q = $request->query->get('q');
        $withoutMention = $request->query->get('cond') == 'noment';
        $response = null;
        $redis = $this->get('snc_redis.default');

        if ($q && strlen($q) >= 2) {
          $index = $this->get('fos_elastica.index.website');
          $res = $index->request('_suggest', 'POST', array(
              'user' => array(
                  'text' => $q,
                  'completion' => array(
                      'field' => 'suggestions'
                  )
              )
          ));
          $usernames = array();
          foreach ($res->getData()['user'][0]['options'] as $option) {
            $usernames[] = $option['text'];
          }
          foreach ($usernames as $username) {
            $result = $redis->hgetall("user:$username");
            $result['type'] = 'user';
            if ($withoutMention) $result['value'] = substr($result['value'], 1);
            $result['url'] = $this->generateUrl('adstacy_app_user_profile', array('username' => $username));
            $results[] = $result;
          }
        }
        $response = new JsonResponse();
        $response->setData($results);
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);

        return $response;
    }

    /**
     * Return tag suggestions.
     */
    public function tagsAction()
    {
        $request = $this->getRequest();
        $results = array();
        $q = $request->query->get('q');
        $response = null;
        $redis = $this->get('snc_redis.default');
        $tags = explode(' ', $q);
        $cnt = count($tags);
        $prevTags = array_slice($tags, 0, $cnt - 1);
        $cntPrevTags = count($prevTags);
        $prevTags = implode(' ', $prevTags);
        $q = $tags[$cnt - 1];

        if ($q && strlen($q) >= 2) {
          $rank = $redis->zrank('tags', $q);
          $possibilities = $redis->zrange('tags', $rank + 1, $rank + 50);
          $tags = array();
          foreach ($possibilities as $possibility) {
            if (strpos($possibility, $q) === false) break;
            $len = strlen($possibility);
            if ($possibility[$len - 1] == '*') {
              $tag = substr($possibility, 0, $len - 1);
              if ($cntPrevTags > 0) {
                  $completeTag = $prevTags.' '.$tag;
              } else {
                  $completeTag = $tag;
              }
              $results[] = array(
                  'value' => $completeTag,
                  'tokens' => array($tag),
                  'url' => $this->generateUrl('adstacy_app_search', array('q' => $completeTag))
              );
            }
          }
        }
        $response = new JsonResponse();
        $response->setData($results);
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);

        return $response;
    }
}
