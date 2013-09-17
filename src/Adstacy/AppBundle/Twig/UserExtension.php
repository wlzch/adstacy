<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Ad;

/**
 * User extension
 */
class UserExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'profile_pic' => new \Twig_Function_Method($this, 'getProfilePicture', array('is_safe' => array('html'))),
            'user_followings_has_promote' => new \Twig_Function_Method($this, 'userFollowingsHasPromote')
        );
    }

    /**
     * get user profile picture
     */
    public function getProfilePicture(User $user)
    {
        return $this->container->get('adstacy.helper.user')->getProfilePicture($user);
    }

    /**
     * Check whether $user's followings has promote $ad
     *
     * @param User
     * @param Ad
     *
     * @return boolean
     */
    public function userFollowingsHasPromote(User $user, Ad $ad)
    {
        $repo = $this->container->get('doctrine')->getRepository('AdstacyAppBundle:User');
        $_user = $repo->findFollowingsPromotes($user);

        return $_user->hasFollowingsPromote($ad);
    }

    public function getName()
    {
        return 'adstacy_user_extension';
    }
}

