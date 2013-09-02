<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Adstacy\AppBundle\Entity\User;

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
        );
    }

    /**
     * get user profile picture
     */
    public function getProfilePicture(User $user)
    {
        $uploaderHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        if ($user->getImage()) {
            return $cacheManager->getBrowserPath($uploaderHelper->asset($user, 'image'), 'profile_pic');
        }
        if ($user->getProfilePicture()) {
            return $user->getProfilePicture();
        }

        return $this->container->getParameter('no_profpic_img');
    }

    public function getName()
    {
        return 'adstacy_user_extension';
    }
}

