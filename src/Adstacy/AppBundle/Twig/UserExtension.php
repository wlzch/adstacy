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
        $uploaderHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        if ($user->getImage() && $user->getImagename()) {
            return $cacheManager->getBrowserPath($uploaderHelper->asset($user, 'image'), 'profile_pic');
        }
        if ($user->getProfilePicture()) {
            return $user->getProfilePicture();
        }

        return $this->container->getParameter('no_profpic_img');
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

