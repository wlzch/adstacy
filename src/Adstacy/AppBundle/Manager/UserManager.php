<?php

namespace Adstacy\AppBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Adstacy\AppBundle\Entity\User;

class UserManager
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Suggest who to follow
     * The suggestion algorithm will find any common followings. Then will fallback to popular user
     *
     * @param User $to
     */
    public function suggestFollow(User $user)
    {
        $repo = $this->container->get('doctrine')->getManager()->getRepository('AdstacyAppBundle:User');
        $redis = $this->container->get('snc_redis.default');

        $recommendation = array();
        $followings = $user->getFollowings();
        $followingsId = array();

        foreach ($followings as $following) {
            $followingsId[] = $following->getId();
        }

        foreach ($followings as $following) {
            foreach ($following->getFollowings() as $followingsFollowing) {
                $followingsFollowingId = $followingsFollowing->getId();
                if (!in_array($followingsFollowingId, $followingsId)) {
                    if (isset($recommendation[$followingsFollowingId])) {
                        $recommendation[$followingsFollowingId]++;
                    } else {
                        $recommendation[$followingsFollowingId] = 1;
                    }
                }
            }
        }

        foreach ($repo->findPopularUsers() as $popularUser) {
            if (!isset($recommendation[$popularUser->getId()])) {
                if (!in_array($popularUser->getId(), $followingsId)) {
                    $recommendation[$popularUser->getId()] = 0;
                }
            }
        }

        $redisKey = 'recommendation:'.$user->getUsername();
        $redis->del($redisKey);
        $cmd = $redis->createCommand('zadd');
        $cmd->setArguments(array($redisKey, $recommendation));
        $redis->executeCommand($cmd);
    }
}
