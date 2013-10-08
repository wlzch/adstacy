<?php

namespace Adstacy\OAuthBundle\API;

use TwitterAPIExchange;
use Adstacy\OAuthBundle\Helper\TwitterHelper;

class TwitterAPI
{
    private $twitter;

    public function __construct(TwitterAPIExchange $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Get ids of friends
     *
     * @param string twitter user id
     */
    public function getFollowings($userid)
    {
        $qs = '?user_id='.$userid.'&stringify_ids=true';
        $res = $this->twitter->setGetField($qs)
                ->buildOauth(TwitterHelper::FRIENDS_URL, 'GET')
                ->performRequest();
        $res = json_decode($res);

        return $res->ids;
    }

    /**
     * Get ids of $userid followers
     *
     * @param string twitter user id
     */
    public function getFollowers($userid)
    {
        $qs = '?user_id='.$userid.'&stringify_ids=true';
        $res = $this->twitter->setGetField($qs)
                ->buildOauth(TwitterHelper::FOLLOWERS_URL, 'GET')
                ->performRequest();
        $res = json_decode($res);

        return $res->ids;
    }
}
