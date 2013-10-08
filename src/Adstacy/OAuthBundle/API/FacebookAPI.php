<?php

namespace Adstacy\OAuthBundle\API;

use Facebook;

class FacebookAPI
{
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Get ids of friends
     *
     * @param string $accessToken
     */
    public function getFriends($accessToken)
    {
        $this->facebook->setAccessToken($accessToken);
        $result = $this->facebook->api('/me/friends');
        $friends = $result['data'];
        $facebookIds = array();
        foreach ($friends as $friend) {
            $facebookIds[] = $friend['id'];
        }

        return $facebookIds;
    }
}
