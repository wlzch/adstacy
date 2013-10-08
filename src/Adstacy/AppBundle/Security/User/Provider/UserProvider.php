<?php

namespace Adstacy\AppBundle\Security\User\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Helper\Twitter;

/**
 * Bridging FOSUser dengan HWIOAuth
 *
 */
class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     */
    public function __construct(UserManagerInterface $userManager, Session $session, Router $router, ContainerInterface $container)
    {
        $this->userManager = $userManager;
        $this->session = $session;
        $this->router = $router;
        $this->properties  = array('facebook' => 'facebookId', 'twitter' => 'twitterId');
        $this->container = $container;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException('Username/email or password is not valid');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     *
     * Terdapat 4 kondisi
     * 1. User belum pernah connect dengan $service
     *    1.1 Email telah terdaftar
     *        Koneksikan user tersebut dengan $service
     *    1.2 Username telah terdaftar
     *        Buat user baru dengan username yang unik
     *    1.3 Username/email belum terdaftar
     *        Buat user baru dengan username
     * 2. User telah terconnect dengan $service
     *    Langsung ambil user tersebut
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        $setter_username = $setter.'Username';
        $setter_real_name = $setter.'RealName';

        //when the user is registrating
        if (null === $user) {
            if ($email && $_user = $this->userManager->findUserByEmail($email)) {
              $user = $_user;
            } else {
                // create new user here
                $user = $this->userManager->createUser();
                $nickname = $response->getNickname();

                if ($this->userManager->findUserByUsername($nickname)) {
                  // username exists
                  $user->setUsername(uniqid($nickname));
                } else {
                  $user->setUsername($nickname);
                }
                $user->setRealName($response->getRealName());
                $user->setEmail($response->getEmail());
                $user->setPassword(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setProfilePicture($response->getProfilePicture());
                $user->setEnabled(true);
                // redirect to settings if new user
                $path = $this->router->getRouteCollection()->get('fos_user_profile_edit')->getPattern();
                $this->session->set('_security.main.target_path', $path);
                $this->notifyFriends($service, $user, $response);
            }
            $user->$setter_id($username);
            $user->$setter_username($response->getNickname());
            $user->$setter_real_name($response->getRealName());
            $user->setProfilePicture($response->getProfilePicture());
        }

        if (!$user->getProfilePicture()) {
            $user->setProfilePicture($response->getProfilePicture());
        }
        //update access token
        $user->$setter_token($response->getAccessToken());
        $user->addRole('ROLE_'.strtoupper($service));
        $this->userManager->updateUser($user);
 
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function connect($user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
 
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
 
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        $setter_username = $setter.'Username';
        $setter_real_name = $setter.'RealName';
 
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $previousUser->$setter_username(null);
            $previousUser->$setter_real_name(null);
            $previousUser->setProfilePicture(null);
            $this->userManager->updateUser($previousUser);
        }
 
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $user->$setter_username($response->getNickname());
        $user->$setter_real_name($response->getRealName());
        $user->setProfilePicture($response->getProfilePicture());
        $user->addRole('ROLE_'.strtoupper($service));
 
        $this->userManager->updateUser($user);
    }

    /**
     * Notify friends that $user has joined Adstacy!
     *
     * @param string $service facebook|twitter
     * @param User $user
     * @param UserResponseInterface $response
     */
    private function notifyFriends($service, User $user, UserResponseInterface $response)
    {
        $friends = array();
        $repo = $this->container->get('doctrine')->getRepository('AdstacyAppBundle:User');
        if ($service == 'facebook') {
            $facebookAPI = $this->container->get('adstacy.oauth.facebook_api');
            $facebookIds = $facebookAPI->getFriends($response->getAccessToken());
            $friends = $repo->findByFacebookId($facebookIds);
        } else if ($service == 'twitter') {
            $twitterAPI = $this->container->get('adstacy.oauth.twitter_api'); 
            $followerIds = $twitterAPI->getFollowers($response->getUsername());
            $friends = $repo->findByTwitterId($followerIds);
        }

        $notificationManager = $this->container->get('adstacy.notification.manager');
        foreach ($friends as $friend) {
            $notificationManager->save($user, $friend, null, false, $service.'_friend_join');
        }
    }

    /**
     * Gets the property for the response.
     *
     * @param UserResponseInterface $response
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Adstacy\\AppBundle\\Entity\\User';
    }
}
