<?php

namespace Adstacy\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\Serializer\SerializationContext;

class UserController extends ApiController
{
    /**
     * Search users
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
     * Return follower's and followings.
     * Used in user autocomplete prefetching
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
     * Return informations about user
     *
     * @param string username
     */
    public function showAction($username)
    {
        $request = $this->getRequest();
        $limit = $this->getParameter('max_ads_per_page');

        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };

        $ads = $user->getAds()->slice(0, $limit);
        $res = array(
            'data' => array(
                'user' => $user,
                'ads' => $ads
            ),
            'meta' => array(
                'url_followers' => $this->generateUrl('adstacy_api_user_followers', array('username' => $username)),
                'url_followings' => $this->generateUrl('adstacy_api_user_followings', array('username' => $username)),
                'url_promotes' => $this->generateUrl('adstacy_api_user_promotes', array('username' => $username)),
                'next' => $this->generateUrl('adstacy_api_user_ads', array('username' => $username))
            )
        );

        return $this->response($res, 'user_show');
    }

    /**
     * Return ads by $username
     *
     * @param string $username
     */
    public function listAdsAction($username)
    {
        return $this->listAds($username, 'ads');
    }

    /**
     * Return promotes by $username
     *
     * @param string $username
     */
    public function listPromotesAction($username)
    {
        return $this->listAds($username, 'promotes');
    }

    private function listAds($username, $action)
    {
        $request = $this->getRequest();
        $id = $request->query->get('id');
        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };
        $limit = $this->getParameter('max_ads_per_page');

        if ($action == 'ads') {
            $ads = $this->getRepository('AdstacyAppBundle:Ad')->findByUser($user, $id, $limit);
        } elseif ($action == 'promotes') {
            $ads = $this->getRepository('AdstacyAppBundle:Ad')->findByPromote($user, $id, $limit);
        }
        if (count($ads) <= 0) {
            return $this->noResult();
        }
        $lastId = $ads[count($ads) - 1]->getId();

        $res = array(
            'data' => array(
                'ads' => $ads
            ),
            'meta' => array(
                'next' => $this->generateUrl("adstacy_api_user_$action", array('username' => $username, 'id' => $lastId))
            )
        );

        return $this->response($res, 'ad_list');
    }

    /**
     * Return $username's followers
     *
     * @param string $username
     */
    public function listFollowersAction($username)
    {
        return $this->listUsers($username, 'followers');
    }

    /**
     * Return $username's followings
     *
     * @param string $username
     */
    public function listFollowingsAction($username)
    {
        return $this->listUsers($username, 'followings');
    }

    private function listUsers($username, $action)
    {
        $request = $this->getRequest();
        $id = $request->query->get('id');
        if (($user = $this->getRepository('AdstacyAppBundle:User')->findOneByUsername($username)) == false) {
            throw $this->createNotFoundException();
        };
        $limit = $this->getParameter('max_users_per_page');

        if ($action == 'followers') {
            $query = $this->getRepository('AdstacyAppBundle:User')->findFollowersByUserQuery($user);
        } elseif ($action == 'followings') {
            $query = $this->getRepository('AdstacyAppBundle:User')->findFollowingsByUserQuery($user);
        }
        $paginator = $this->getDoctrinePaginator($query, $limit);
        if ($paginator->getNbPages() <= 0) {
            return $this->noResult();
        }

        $res = array(
            'data' => array(
                'users' => $paginator->getCurrentPageResults()->getArrayCopy()
            ),
            'meta' => array()
        );
        if ($paginator->haveToPaginate()) {
            $res['meta']['next'] = $this->generateUrl("adstacy_api_user_$action", array('username' => $username, 'page' => $paginator->getNextPage()));
        }

        return $this->response($res, 'user_list');
    }
}
