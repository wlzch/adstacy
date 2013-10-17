<?php

namespace Adstacy\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\Serializer\SerializationContext;

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
     * Return follower's and followings
     *
     * @Secure(roles="ROLE_USER")
     */
    public function networkAction()
    {
        $request = $this->getRequest();
        $withoutMention = $request->query->get('cond') == 'noment';

        $authUser = $this->getUser();
        $followers = $authUser->getFollowers();
        $followings = $authUser->getFollowings();
        $users = array();
        foreach ($followers as $follower) {
            $users[] = $follower;
        }
        foreach ($followings as $following) {
            $users[] = $following;
        }
        $userHelper = $this->get('adstacy.helper.user');

        $results = array();
        foreach ($users as $user) {
            $result = array(
                'id' => $user->getId(),
                'name' => $user->getRealName(),
                'avatar' => $userHelper->getProfilePicture($user, false),
                'value' => '@'.$user->getUsername(),
                'type' => 'user',
                'username' => $user->getUsername(),
                'url' => $this->generateUrl('adstacy_app_user_profile', array('username' => $user->getUsername()))
            );
            if ($withoutMention) {
                $result['value'] = substr($result['value'], 1);
            }
            $results[] = $result;
        }

        $response = new JsonResponse($results);
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

    /**
     * Return informations about user
     *
     * @param string username
     */
    public function showUserAction($username)
    {
        $request = $this->getRequest();
        $limit = $this->getParameter('max_ads_per_page');

        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };
        $serializer = $this->get('serializer');

        $ads = $user->getAds()->slice(0, $limit);
        $res = array(
            'data' => array(
                'user' => $user,
                'ads' => $ads
            ),
            'meta' => array(
                'url_followers' => $this->generateUrl('adstacy_app_api_user_followers', array('username' => $username)),
                'url_followings' => $this->generateUrl('adstacy_app_api_user_followings', array('username' => $username)),
                'url_promotes' => $this->generateUrl('adstacy_app_api_user_promotes', array('username' => $username)),
                'next' => $this->generateUrl('adstacy_app_api_user_ads', array('username' => $username))
            )
        );

        return new Response($serializer->serialize($res, 'json', SerializationContext::create()->setGroups(array('user_show'))));
    }

    /**
     * Return ads by $username
     *
     * @param string $username
     */
    public function listAdsAction($username)
    {
        $request = $this->getRequest();
        $id = $request->query->get('id');
        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };
        $limit = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findByUser($user, $id, $limit);
        if (count($ads) <= 0) {
            return new JsonResponse(array('data' => array()));
        }
        $lastId = $ads[count($ads) - 1]->getId();

        $res = array(
            'data' => array(
                'ads' => $ads
            ),
            'meta' => array(
                'next' => $this->generateUrl('adstacy_app_api_user_ads', array('username' => $username, 'id' => $lastId))
            )
        );
        $serializer = $this->get('serializer');

        return new Response($serializer->serialize($res, 'json', SerializationContext::create()->setGroups(array('ad_list'))));
    }

    /**
     * Return promotes by $username
     *
     * @param string $username
     */
    public function listPromotesAction($username)
    {
        $request = $this->getRequest();
        $id = $request->query->get('id');
        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };
        $limit = $this->getParameter('max_ads_per_page');
        $ads = $this->getRepository('AdstacyAppBundle:Ad')->findByPromote($user, $id, $limit);
        if (count($ads) <= 0) {
            return new JsonResponse(array('data' => array()));
        }
        $lastId = $ads[count($ads) - 1]->getId();

        $res = array(
            'data' => array(
                'ads' => $ads
            ),
            'meta' => array(
                'next' => $this->generateUrl('adstacy_app_api_user_promotes', array('username' => $username, 'id' => $lastId))
            )
        );
        $serializer = $this->get('serializer');

        return new Response($serializer->serialize($res, 'json', SerializationContext::create()->setGroups(array('ad_list'))));
    }

}
