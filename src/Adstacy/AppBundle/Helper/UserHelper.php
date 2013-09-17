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
}
