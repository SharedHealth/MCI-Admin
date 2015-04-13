<?php

namespace Mci\Bundle\UserBundle\Security;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EquatableInterface
{
    const ADMIN_PROFILE_KEY = 'mci_supervisor';
    /**
     * @Type("string")
     * @SerializedName("id")
     */
    private $id;

    /**
     * @Type("string")
     * @SerializedName("name")
     */
    private $name;

    /**
     * @Type("string")
     * @SerializedName("email")
     */
    private $email;

    /**
     * @Type("boolean")
     * @SerializedName("is_active")
     */
    private $active;

    private $roles;

    /**
     * @Type("string")
     * @SerializedName("access_token")
     */
    private $token;

    /**
     * @Type("array")
     */
    private $profiles;

    /**
     * @Type("array")
     */
    private $groups;

    private static function getSupportedRoles()
    {
        return array('ROLE_MCI_ADMIN', 'ROLE_MCI_APPROVER');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->name;
    }


    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        if($this->roles == null) {
            $this->populateRoles();
        }

        return $this->roles;
    }

    private function populateRoles()
    {
        $roles = array();

        foreach($this->groups as $group) {
            $role = $this->getRoleWithPrefix($group);
            if(in_array($role, self::getSupportedRoles())) {
                $roles[] = $role;
            }
        }

        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getCatchments()
    {
        $adminProfile = $this->getAdminProfile();

        if(!empty($adminProfile) && isset($adminProfile['catchment'])) {
            return $adminProfile['catchment'];
        }

        return array();
    }

    private function getAdminProfile()
    {
        if(empty($this->profiles)) {
            return null;
        }

        foreach($this->profiles as $profile) {
            if(isset($profile['name']) && $profile['name'] == self::ADMIN_PROFILE_KEY) {
                return $profile;
            }
        }

        return null;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString()
    {
        return $this->name . "";
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->token !== $user->getToken()) {
            return false;
        }

        if ($this->name !== $user->getName()) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        $this->roles = null;

    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param $group
     * @return string
     */
    private function getRoleWithPrefix($group)
    {
        return 'ROLE_' . strtoupper(str_replace(' ', '_', $group));
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
