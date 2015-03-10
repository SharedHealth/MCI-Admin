<?php

namespace Mci\Bundle\UserBundle\Security;

use Mci\Bundle\UserBundle\Exception\DisabledException;
use Mci\Bundle\UserBundle\Exception\InsufficientAuthenticationException;
use Mci\Bundle\UserBundle\Exception\UsernameNotFoundException;
use Mci\Bundle\UserBundle\Service\SsoClient;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SsoUserAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var SsoClient
     */
    private $ssoClient;

    public function __construct(SsoClient $ssoClient)
    {
        $this->ssoClient = $ssoClient;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new PreAuthenticatedToken(
            '',
            array(
                'username' => $username,
                'password' => $password
            ),
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $credential = $token->getCredentials();

        if($token->getUser() != null) {
            return $token;
        }

        $user = $this
            ->ssoClient
            ->getUserByEmailAndPassword($credential['username'], $credential['password']);

        if($user != null) {
            $user->setGroups(array('MCI_ADMIN'));
        }

        if( null !== $ex = $this->handleExceptions($user) ) {
            throw $ex;
        }

        return new UsernamePasswordToken(
            $user,
            $credential,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param User $user
     * @return bool
     */
    private function isPrivilegedUser(User $user)
    {
        $roles = $user->getRoles();
        return empty($roles);
    }

    /**
     * @param $user
     * @return AuthenticationException|null
     */
    private function handleExceptions(User $user = null)
    {
        if (null == $user) {
            return new UsernameNotFoundException('Invalid username or password');
        }

        if (!$user->isActive()) {
            return new DisabledException('Invalid username or password');
        }


        if ($this->isPrivilegedUser($user)) {
            return new InsufficientAuthenticationException('Invalid username or password');
        }

        return null;
    }
}