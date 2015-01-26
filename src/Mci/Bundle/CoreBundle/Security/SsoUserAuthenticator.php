<?php

namespace Mci\Bundle\CoreBundle\Security;

use Mci\Bundle\CoreBundle\Service\SsoClient;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SsoUserAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var SsoClient
     */
    private $ssoClient;

    public function __construct(SsoClient $ssoClient)
    {
        $this->ssoClient = $ssoClient;
    }

    public function createToken(Request $request, $providerKey)
    {
        return new PreAuthenticatedToken(
            '',
            $request->cookies->get('SHR_IDENTITY_TOKEN'),
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $session = $token->getCredentials();

        $user = $this
            ->ssoClient
            ->getUserFromToken($session);

        if($user == null) {
            $user = new User();
            $user->setRoles(array('IS_AUTHENTICATED_ANONYMOUSLY'));

        }

        return new PreAuthenticatedToken(
            $user,
            $session,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}