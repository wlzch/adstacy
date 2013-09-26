<?php

namespace Adstacy\AppBundle\Helper;

use Adstacy\AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserHelper
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * get user profile picture
     *
     * @param User $user
     * @param boolean $absolute
     */
    public function getProfilePicture(User $user, $absolute = false)
    {
        $uploaderHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $host = $this->container->getParameter('adstacy.host');
        if ($user->getImage() && $user->getImagename()) {
            $path = $cacheManager->getBrowserPath($uploaderHelper->asset($user, 'image'), 'profile_pic');
            if ($absolute) return $host.$path;
            return $path;
        }
        if ($user->getProfilePicture()) {
            return $user->getProfilePicture();
        }

        $path = $this->container->getParameter('no_profpic_img');
        if ($absolute) return $host.$path;
        return $path;
    }
}
