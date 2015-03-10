<?php

namespace Mci\Bundle\UserBundle\Exception;

class InsufficientAuthenticationException extends \Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException
{
    /**
     * {@inheritDoc}
     */
    public function getMessageKey()
    {
        return 'User does not have sufficient privileges!';
    }
}
