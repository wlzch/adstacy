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
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Imagine\Gd\Imagine;
use Imagine\Filter\Advanced\RelativeResize;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="Adstacy\AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users", indexes={
 *   @ORM\Index(name="user_username", columns={"username_canonical"}),
 *   @ORM\Index(name="user_email", columns={"email_canonical"})
 * })
 * @UniqueEntity(fields="emailCanonical", message="user.email.unique", errorPath="email", groups={"Registration", "Profile"})
 * @UniqueEntity(fields="usernameCanonical", message="user.username.unique", errorPath="username", groups={"Registration", "Profile"})
 * @Assert\Callback(methods={"isUsernameValid"}, groups={"Registration", "Profile"})
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("all")
 */
class User implements UserInterface, GroupableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $id;

    /**
     * @Assert\NotBlank(message="user.username.not_blank", groups={"Registration", "Profile"})
     * @Assert\Length(
     *    min = 2,
     *    max = 100,
     *    minMessage = "user.username.min",
     *    maxMessage = "user.username.max",
     *    groups = {"Registration", "Profile"}
     *  )
     * @ORM\Column(type="string", length=100, nullable=false)
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show", "comment_list"})
     */
    private $username;

    /**
     * @ORM\Column(name="username_canonical", type="string", length=100, nullable=false, unique=true)
     */
    private $usernameCanonical;

    /**
     * @Assert\NotBlank(message="user.email.not_blank", groups={"Registration", "Profile"})
     * @Assert\Email(message="user.email.email", groups={"Registration", "Profile"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="email_canonical", type="string", length=255, nullable=true, unique=true)
     */
    private $emailCanonical;

    /**
     * @Assert\Image(
     *    maxSize = "5M",
     *    mimeTypes = {"image/png", "image/jpeg", "image/pjpeg"},
     *    minWidth = 100,
     *    maxSizeMessage = "image.file.max_size",
     *    mimeTypesMessage = "image.file.mime_types",
     *    minWidthMessage = "image.file.min_width",
     *    groups = {"Profile"}
     * )
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="imagename")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagename;

    /**
     * @Assert\NotBlank(message="user.realname.not_blank", groups={"Registration", "Profile"})
     * @ORM\Column(name="real_name", type="string", length=100)
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show", "comment_list"})
     */
    private $realName;

    /**
     * @ORM\OneToMany(targetEntity="Ad", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    private $ads;

    /**
     * @ORM\Column(name="ads_count", type="integer")
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $adsCount;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followings", fetch="EXTRA_LAZY")
     **/
    private $followers;

    /**
     * @ORM\Column(name="followers_count", type="integer")
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $followersCount;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="follow",
     *      joinColumns={
     *        @ORM\JoinColumn(name="follower", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *        @ORM\JoinColumn(name="followed", referencedColumnName="id")
     *      }
     * )
     **/
    private $followings;

    /**
     * @ORM\Column(name="following_counts", type="integer")
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $followingsCount;

    /**
     * @Assert\Length(
     *    max = 255,
     *    maxMessage = "user.about.max",
     *    groups = {"Profile"}
     *  )
     * @ORM\Column(name="about", type="string", length=255, nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $about;

    /**
     * @ORM\OneToMany(targetEntity="PromoteAd", mappedBy="user", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $promotes;

    /**
     * @ORM\Column(name="promotes_count", type="integer")
     * @JMS\Expose
     * @JMS\Groups({"user_show", "user_list", "ad_list", "ad_show"})
     */
    private $promotesCount;

    /**
     * @ORM\OneToMany(targetEntity="Adstacy\NotificationBundle\Entity\Notification", mappedBy="to", cascade={"persist"})
     */
    private $notifications;

    /**
     * @ORM\Column(name="notifications_count", type="integer", nullable=true)
     */
    private $notificationsCount;

    /**
     * @ORM\Column(name="profile_picture", type="string", length=255, nullable=true)
     */
    private $profilePicture;

    /**
     * @ORM\Column(name="favourite_tags", type="simple_array", nullable=true)
     */
    private $favouriteTags;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=75, nullable=true, unique=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(name="facebook_username", type="string", length=75, nullable=true, unique=true)
     */
    private $facebookUsername;

    /**
     * @ORM\Column(name="facebook_real_name", type="string", length=100, nullable=true)
     */
    private $facebookRealName;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    private $facebookAccessToken;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=75, nullable=true, unique=true)
     */
    private $twitterId;

    /**
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    private $twitterAccessToken;

    /**
     * @ORM\Column(name="twitter_username", type="string", length=75, nullable=true, unique=true)
     */
    private $twitterUsername;

    /**
     * @ORM\Column(name="twitter_real_name", type="string", length=100, nullable=true)
     */
    private $twitterRealName;

    /**
     * Hack for VichUploaderBundle
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * The salt to use for hashing
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="user.password.not_blank", groups={"Registration", "ResetPassword", "ChangePassword"})
     * @Assert\Length(
     *    min = 5,
     *    minMessage = "user.password.min",
     *    groups = {"Registration", "Profile", "ResetPassword", "ChangePassword"}
     * )
     * Plain password. Used for model validation. Must not be persisted.
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="subscription", type="boolean", nullable=true)
     */
    private $subscription;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var Collection
     */
    private $groups;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $locked;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $expired;

    /**
     * @var \DateTime
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var boolean
     * @ORM\Column(name="credentials_expired", type="boolean")
     */
    private $credentialsExpired;

    /**
     * @var \DateTime
     * @ORM\Column(name="credentials_expire_at", type="datetime", nullable=true)
     */
    private $credentialsExpireAt;

    /**
     * Used in elasticsearch auto complete feature
     */
    private $suggestions;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = true;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
        $this->adsCount = 0;
        $this->followersCount = 0;
        $this->followingsCount = 0;
        $this->promotesCount = 0;
        $this->notificationsCount = 0;
        $this->suggestions = array();
        $this->subscription = true;
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
               $context->addViolationAt('username', 'user.username.valid');
           }
           if (in_array($this->username, array(
            'admin', 'explore', 'stream', 'trending', 'who-to-follow', 'settings', 'search', 'contact-us',
            'login', 'logout', 'register', 'notifications', 'profile', 'resetting', 'upload', 'upload-url',
            'tags'
           ))) {
               $context->addViolationAt('username', 'user.username.reserved');
           }
       }
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
        return $this->promotesCount ?: 0;
    }

    /**
     * Increase ads count
     */
    public function increaseAdsCount()
    {
        $this->setAdsCount($this->getAdsCount() + 1);
    }

    /**
     * Increase ads count
     */
    public function decreaseAdsCount()
    {
        $this->setAdsCount($this->getAdsCount() - 1);
    }

    /**
     * Set adsCount
     *
     * @param integer $adsCount
     * @return User
     */
    public function setAdsCount($adsCount)
    {
        $this->adsCount = $adsCount;
    
        return $this;
    }

    /**
     * Get adsCount
     *
     * @return integer 
     */
    public function getAdsCount()
    {
        return $this->adsCount ?: 0;
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
        $followers->addFollowing($this);
    
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
        $followers->removeFollowing($this);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers ?: array();
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
        return $this->followersCount ?: 0;
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
        return $this->followingsCount ?: 0;
    }

    /**
     * Get followings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowings()
    {
        return $this->followings ?: array();
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
     * Checks wheter user has promote $ad
     *
     * @param Ad $ad
     *
     * @return boolean
     */
    public function hasPromote(Ad $ad)
    {
        // will be lazyloaded
        foreach ($this->getPromotes() as $promote) {
            if ($promote->getAd() === $ad) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks wheter user has follow $user
     *
     * @param User $user
     *
     * @return boolean
     */
    public function hasFollowUser(User $user)
    {
        return $this->getFollowings()->contains($user);
    }

    /**
     * Set realName
     *
     * @param string $realName
     * @return User
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
    
        return $this;
    }

    /**
     * Get realName
     *
     * @return string 
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Add ads
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ads
     * @return User
     */
    public function addAd(\Adstacy\AppBundle\Entity\Ad $ads)
    {
        $this->ads[] = $ads;
        $this->setAdsCount($this->getAdsCount() + 1);
    
        return $this;
    }

    /**
     * Remove ads
     *
     * @param \Adstacy\AppBundle\Entity\Ad $ads
     */
    public function removeAd(\Adstacy\AppBundle\Entity\Ad $ads)
    {
        $this->ads->removeElement($ads);
        $this->setAdsCount($this->getAdsCount() - 1);
    }

    /**
     * Get ads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAds()
    {
        return $this->ads ?: array();
    }

    public function setImage(File $image = null)
    {
        if ($image && $this->image != $image) {
            $originalImage = $image;
            $imagine = new Imagine();
            $image = $imagine->open($image);
            $size = $image->getSize();
            if ($size->getWidth() > 320) {
                $relativeResize = new RelativeResize('widen', 320);
                $image = $relativeResize->apply($image);
                $image->save($originalImage->getRealPath(), array('format' => $originalImage->guessClientExtension()));
            }
            $this->image = $originalImage;
            // only if there is any field changes
            $this->setUpdated(new \Datetime());
        }
    
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imagename
     *
     * @param string $imagename
     * @return User
     */
    public function setImagename($imagename)
    {
        if ($imagename) {
            $this->imagename = $imagename;
        }
    
        return $this;
    }

    /**
     * Get imagename
     *
     * @return string 
     */
    public function getImagename()
    {
        return $this->imagename;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add promotes
     *
     * @param \Adstacy\AppBundle\Entity\PromoteAd $promotes
     * @return User
     */
    public function addPromote(\Adstacy\AppBundle\Entity\PromoteAd $promotes)
    {
        $this->promotes[] = $promotes;
        $this->setPromotesCount($this->getPromotesCount() + 1);
        $promotes->setUser($this);
    
        return $this;
    }

    /**
     * Remove promotes
     *
     * @param \Adstacy\AppBundle\Entity\PromoteAd $promotes
     */
    public function removePromote(\Adstacy\AppBundle\Entity\PromoteAd $promotes)
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
     * Add notifications
     *
     * @param \Adstacy\NotificationBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\Adstacy\NotificationBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
        $this->setNotificationsCount($this->getNotificationsCount() + 1);
    
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Adstacy\NotificationBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Adstacy\NotificationBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
        $this->setNotificationsCount($this->getNotificationsCount() - 1);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set notificationsCount
     *
     * @param integer $notificationsCount
     * @return User
     */
    public function setNotificationsCount($notificationsCount)
    {
        $this->notificationsCount = $notificationsCount;
    
        return $this;
    }

    /**
     * Get notificationsCount
     *
     * @return integer 
     */
    public function getNotificationsCount()
    {
        return $this->notificationsCount ?: 0;
    }

    /**
     * Set subscription
     *
     * @param boolean $subscription
     * @return User
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    
        return $this;
    }

    /**
     * Get subscription
     *
     * @return boolean 
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Is subscribed
     *
     * @return $boolean
     */
    public function isSubscribed()
    {
        return $this->subscription == true;
    }

    /**
     * Suggestions is returned as array of username and real name
     *
     * @return array
     */
    public function getSuggestions()
    {
      return array(
        'input' => array(
          $this->getUsername(), $this->getRealName()
        ),
        'output' => $this->getUsername()
      );
    }

    /**
     * Set favouriteTags
     *
     * @param array $favouriteTags
     * @return User
     */
    public function setFavouriteTags($favouriteTags)
    {
        $this->favouriteTags = array_unique($favouriteTags);
    
        return $this;
    }

    /**
     * Add favourite tag
     *
     * @param string $favouriteTag
     */
    public function addFavouriteTag($favouriteTag)
    {
        if (!in_array($favouriteTag, $this->favouriteTags)) {
            $this->favouriteTags[] = $favouriteTag;
        }
    }

    /**
     * Remove favourite tag
     *
     * @param string $favouriteTag
     */
    public function removeFavouriteTag($favouriteTag)
    {
        if (($key = array_search($favouriteTag, $this->favouriteTags)) !== false) {
            unset($this->favouriteTags[$key]);
        }
    }

    /**
     * Get favouriteTags
     *
     * @return array 
     */
    public function getFavouriteTags()
    {
        return $this->favouriteTags;
    }
}
