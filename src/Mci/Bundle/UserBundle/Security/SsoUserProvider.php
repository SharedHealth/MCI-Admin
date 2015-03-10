<?php

namespace Mci\Bundle\UserBundle\Security;

use Mci\Bundle\UserBundle\Security\User;
use Mci\Bundle\UserBundle\Service\SsoClient;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SsoUserProvider implements UserProviderInterface
{
    /**
     * @var SsoClient
     */
    private $ssoClient;

    public function __construct(SsoClient $ssoClient)
    {
        $this->ssoClient = $ssoClient;
    }

    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->ssoClient->getUserFromToken($user->getToken());
    }

    public function supportsClass($class)
    {
        return 'Mci\Bundle\UserBundle\Security\User' === $class;
    }
}