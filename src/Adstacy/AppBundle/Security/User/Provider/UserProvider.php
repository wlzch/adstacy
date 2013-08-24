<?php

namespace Adstacy\AppBundle\Security\User\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Adstacy\AppBundle\Entity\User;

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
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
        $this->properties  = array('facebook' => 'facebookId', 'twitter' => 'twitterId');
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
              $user->setEmail($response->getEmail());
              $user->setPassword(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
              $user->setProfilePicture($response->getProfilePicture());
              $user->setEnabled(true);
            }
            $user->$setter_id($username);
            $user->$setter_username($response->getNickname());
            $user->$setter_real_name($response->getRealName());
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
            $this->userManager->updateUser($previousUser);
        }
 
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $user->$setter_username($response->getNickname());
        $user->$setter_real_name($response->getRealName());
        $user->addRole('ROLE_'.strtoupper($service));
 
        $this->userManager->updateUser($user);
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