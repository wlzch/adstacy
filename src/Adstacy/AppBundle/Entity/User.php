<?php

namespace Adstacy\AppBundle\Entity;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\GroupableInterface;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users", indexes={
 *   @ORM\Index(name="user_username", columns={"username_canonical"}),
 *   @ORM\Index(name="user_email", columns={"email_canonical"})
 * })
 * @UniqueEntity(fields="emailCanonical", message="Email does not exists", errorPath="email", groups={"Registration", "Profile"})
 * @UniqueEntity(fields="usernameCanonical", message="Username does not exists", errorPath="username", groups={"Registration", "Profile"})
 * @Assert\Callback(methods={"isUsernameValid"})
 */
class User implements UserInterface, GroupableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank(message="Username cannot be blank", groups={"Registration", "Profile"})
     * @Assert\Length(
     *    min = 2,
     *    max = 100,
     *    minMessage = "Username minimal {{ limit }} karakter",
     *    maxMessage = "Username maksimal {{ limit }} karakter",
     *    groups = {"Registration", "Profile"}
     *  )
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $username;

    /**
     * @ORM\Column(name="username_canonical", type="string", length=100, nullable=false, unique=true)
     */
    protected $usernameCanonical;

    /**
     * @Assert\NotBlank(message="Email cannot be blank", groups={"Registration", "Profile"})
     * @Assert\Email(message="Email tidak valid", groups={"Registration", "Profile"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="email_canonical", type="string", length=255, nullable=true, unique=true)
     */
    protected $emailCanonical;

    /**
     * @ORM\OneToMany(targetEntity="Wall", mappedBy="user")
     */
    private $walls;

    /**
     * @ORM\ManyToMany(targetEntity="Wall", inversedBy="followers")
     * @ORM\JoinTable(name="followed_walls",
     *    joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *    },
     *    inverseJoinColumns={
     *      @ORM\JoinColumn(name="wall_id", referencedColumnName="id")
     *    }
     * )
     */
    private $followedWalls;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followings")
     **/
    private $followers;

    /**
     * @ORM\Column(name="followers_count", type="integer")
     */
    private $followersCount;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="follow",
     *      joinColumns={
     *        @ORM\JoinColumn(name="user_1_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *        @ORM\JoinColumn(name="user_2_id", referencedColumnName="id")
     *      }
     * )
     **/
    private $followings;

    /**
     * @ORM\Column(name="following_counts", type="integer")
     */
    private $followingsCount;

    /**
     * @ORM\Column(name="about", type="string", length=255, nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $interests;

    /**
     * @ORM\ManyToMany(targetEntity="Ad", mappedBy="promotees")
     */
    private $promotes;

    /**
     * @ORM\Column(name="promotes_count", type="integer")
     */
    private $promotesCount;

    /**
     * @ORM\Column(name="profile_picture", type="string", length=255, nullable=true)
     */
    protected $profilePicture;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true, unique=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(name="facebook_username", type="string", length=255, nullable=true, unique=true)
     */
    protected $facebookUsername;

    /**
     * @ORM\Column(name="facebook_real_name", type="string", length=255, nullable=true)
     */
    protected $facebookRealName;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebookAccessToken;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true, unique=true)
     */
    protected $twitterId;

    /**
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    protected $twitterAccessToken;

    /**
     * @ORM\Column(name="twitter_username", type="string", length=255, nullable=true, unique=true)
     */
    protected $twitterUsername;

    /**
     * @ORM\Column(name="twitter_real_name", type="string", length=255, nullable=true)
     */
    protected $twitterRealName;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * The salt to use for hashing
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @Assert\NotBlank(message="Password cannot be blank", groups={"Registration", "ResetPassword", "ChangePassword"})
     * @Assert\Length(
     *    min = 5,
     *    minMessage = "Password requires at least {{ limit }} characters",
     *    groups = {"Registration", "Profile", "ResetPassword", "ChangePassword"}
     * )
     * Plain password. Used for model validation. Must not be persisted.
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $locked;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $expired;

    /**
     * @var \DateTime
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @var boolean
     * @ORM\Column(name="credentials_expired", type="boolean")
     */
    protected $credentialsExpired;

    /**
     * @var \DateTime
     * @ORM\Column(name="credentials_expire_at", type="datetime", nullable=true)
     */
    protected $credentialsExpireAt;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = true;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followersCount = 0;
        $this->followingsCount = 0;
        $this->promotesCount = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id
        ) = $data;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    public function isCredentialsNonExpired()
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired();
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }

    public function isLocked()
    {
        return !$this->isAccountNonLocked();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setCredentialsExpireAt(\DateTime $date = null)
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    /**
     * @param boolean $boolean
     *
     * @return User
     */
    public function setCredentialsExpired($boolean)
    {
        $this->credentialsExpired = $boolean;

        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;

        return $this;
    }

    /**
     * Sets this user to expired.
     *
     * @param Boolean $boolean
     *
     * @return User
     */
    public function setExpired($boolean)
    {
        $this->expired = (Boolean) $boolean;

        return $this;
    }

    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function setLocked($boolean)
    {
        $this->locked = $boolean;

        return $this;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Gets the groups granted to the user.
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    
        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;
    
        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string 
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set profilePicture
     *
     * @param string $profilePicture
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    
        return $this;
    }

    /**
     * Get profilePicture
     *
     * @return string 
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean 
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime 
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;
    
        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * Set twitterAccessToken
     *
     * @param string $twitterAccessToken
     * @return User
     */
    public function setTwitterAccessToken($twitterAccessToken)
    {
        $this->twitterAccessToken = $twitterAccessToken;
    
        return $this;
    }

    /**
     * Get twitterAccessToken
     *
     * @return string 
     */
    public function getTwitterAccessToken()
    {
        return $this->twitterAccessToken;
    }

    public function isUsernameValid(ExecutionContextInterface $context)
   {
       if ($this->username && $this) {
           if (!preg_match('/^[a-zA-Z0-9\.\_]{1,}$/', $this->username)) { // for english chars + numbers only
               $context->addViolationAt('username', 'Username can only contains a-z0-9._');
           }
       }
   }

    /**
     * Set interests
     *
     * @param array $interests
     * @return User
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;
    
        return $this;
    }

    /**
     * Get interests
     *
     * @return array 
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set promotesCount
     *
     * @param integer $promotesCount
     * @return User
     */
    public function setPromotesCount($promotesCount)
    {
        $this->promotesCount = $promotesCount;
    
        return $this;
    }

    /**
     * Get promotesCount
     *
     * @return integer 
     */
    public function getPromotesCount()
    {
        return $this->promotesCount;
    }

    /**
     * Add walls
     *
     * @param \Adstacy\AppBundle\Entity\Wall $walls
     * @return User
     */
    public function addWall(\Adstacy\AppBundle\Entity\Wall $walls)
    {
        $this->walls[] = $walls;
    
        return $this;
    }

    /**
     * Remove walls
     *
     * @param \Adstacy\AppBundle\Entity\Wall $walls
     */
    public function removeWall(\Adstacy\AppBundle\Entity\Wall $walls)
    {
        $this->walls->removeElement($walls);
    }

    /**
     * Get walls
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWalls()
    {
        return $this->walls;
    }

    /**
     * Add followedWalls
     *
     * @param \Adstacy\AppBundle\Entity\Wall $followedWalls
     * @return User
     */
    public function addFollowedWall(\Adstacy\AppBundle\Entity\Wall $followedWalls)
    {
        $this->followedWalls[] = $followedWalls;
    
        return $this;
    }

    /**
     * Remove followedWalls
     *
     * @param \Adstacy\AppBundle\Entity\Wall $followedWalls
     */
    public function removeFollowedWall(\Adstacy\AppBundle\Entity\Wall $followedWalls)
    {
        $this->followedWalls->removeElement($followedWalls);
    }

    /**
     * Get followedWalls
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowedWalls()
    {
        return $this->followedWalls;
    }

    /**
     * Add followers
     *
     * @param \Adstacy\AppBundle\Entity\User $followers
     * @return User
     */
    public function addFollower(\Adstacy\AppBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;
        $this->setFollowersCount($this->getFollowersCount() + 1);
    
        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Adstacy\AppBundle\Entity\User $followers
     */
    public function removeFollower(\Adstacy\AppBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
        $this->setFollowersCount($this->getFollowersCount() - 1);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add following
     *
     * @param \Adstacy\AppBundle\Entity\User $following
     * @return User
     */
    public function addFollowing(\Adstacy\AppBundle\Entity\User $following)
    {
        $this->followings[] = $following;
        $this->setFollowingsCount($this->getFollowingsCount() + 1);
    
        return $this;
    }

    /**
     * Remove following
     *
     * @param \Adstacy\AppBundle\Entity\User $following
     */
    public function removeFollowing(\Adstacy\AppBundle\Entity\User $following)
    {
        $this->followings->removeElement($following);
        $this->setFollowingsCount($this->getFollowingsCount() - 1);
    }

    /**
     * Get following
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowing()
    {
        return $this->followings;
    }

    /**
     * Add promotes
     *
     * @param \Adstacy\AppBundle\Entity\Ad $promotes
     * @return User
     */
    public function addPromote(\Adstacy\AppBundle\Entity\Ad $promotes)
    {
        $this->promotes[] = $promotes;
        $this->setPromotesCount($this->getPromotesCount() + 1);
    
        return $this;
    }

    /**
     * Remove promotes
     *
     * @param \Adstacy\AppBundle\Entity\Ad $promotes
     */
    public function removePromote(\Adstacy\AppBundle\Entity\Ad $promotes)
    {
        $this->promotes->removeElement($promotes);
        $this->setPromotesCount($this->getPromotesCount() - 1);
    }

    /**
     * Get promotes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPromotes()
    {
        return $this->promotes;
    }

    /**
     * Set followersCount
     *
     * @param integer $followersCount
     * @return User
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;
    
        return $this;
    }

    /**
     * Get followersCount
     *
     * @return integer 
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Set followingsCount
     *
     * @param integer $followingsCount
     * @return User
     */
    public function setFollowingsCount($followingsCount)
    {
        $this->followingsCount = $followingsCount;
    
        return $this;
    }

    /**
     * Get followingsCount
     *
     * @return integer 
     */
    public function getFollowingsCount()
    {
        return $this->followingsCount;
    }

    /**
     * Get followings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowings()
    {
        return $this->followings;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;
    
        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set facebookUsername
     *
     * @param string $facebookUsername
     * @return User
     */
    public function setFacebookUsername($facebookUsername)
    {
        $this->facebookUsername = $facebookUsername;
    
        return $this;
    }

    /**
     * Get facebookUsername
     *
     * @return string 
     */
    public function getFacebookUsername()
    {
        return $this->facebookUsername;
    }

    /**
     * Set facebookRealName
     *
     * @param string $facebookRealName
     * @return User
     */
    public function setFacebookRealName($facebookRealName)
    {
        $this->facebookRealName = $facebookRealName;
    
        return $this;
    }

    /**
     * Get facebookRealName
     *
     * @return string 
     */
    public function getFacebookRealName()
    {
        return $this->facebookRealName;
    }

    /**
     * Set twitterUsername
     *
     * @param string $twitterUsername
     * @return User
     */
    public function setTwitterUsername($twitterUsername)
    {
        $this->twitterUsername = $twitterUsername;
    
        return $this;
    }

    /**
     * Get twitterUsername
     *
     * @return string 
     */
    public function getTwitterUsername()
    {
        return $this->twitterUsername;
    }

    /**
     * Set twitterRealName
     *
     * @param string $twitterRealName
     * @return User
     */
    public function setTwitterRealName($twitterRealName)
    {
        $this->twitterRealName = $twitterRealName;
    
        return $this;
    }

    /**
     * Get twitterRealName
     *
     * @return string 
     */
    public function getTwitterRealName()
    {
        return $this->twitterRealName;
    }

    /**
     * Checks where user has promote this ad
     *
     * @param Ad $ad
     *
     * @return boolean
     */
    public function hasPromote(Ad $ad)
    {
        return $this->getPromotes()->contains($ad);
    }
}
