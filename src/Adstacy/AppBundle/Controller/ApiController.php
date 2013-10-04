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
        $response = null;
        $redis = $this->get('snc_redis.default');

        if ($q && strlen($q) >= 2) {
          $rank = $redis->zrank('usernames', $q);
          $possibilities = $redis->zrange('usernames', $rank + 1, $rank + 50);
          $usernames = array();
          foreach ($possibilities as $possibility) {
            if (strpos($possibility, $q) === false) break;
            $len = strlen($possibility);
            if ($possibility[$len - 1] == '*') {
              $usernames[] = substr($possibility, 0, $len - 1);
            }
          }
          foreach ($usernames as $username) {
            $result = $redis->hgetall("user:$username");
            $result['type'] = 'user';
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
     * Get tags
     */
    public function tagsAction()
    {
        $request = $this->getRequest();
        $results = array();
        $q = $request->query->get('q');
        $response = null;
        $redis = $this->get('snc_redis.default');

        if ($q && strlen($q) >= 2) {
          $rank = $redis->zrank('tags', $q);
          $possibilities = $redis->zrange('tags', $rank + 1, $rank + 50);
          $tags = array();
          foreach ($possibilities as $possibility) {
            if (strpos($possibility, $q) === false) break;
            $len = strlen($possibility);
            if ($possibility[$len - 1] == '*') {
              $tag = substr($possibility, 0, $len - 1);
              $results[] = array('value' => $tag, 'tokens' => array($tag));
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
