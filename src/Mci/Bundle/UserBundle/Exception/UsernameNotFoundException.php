<?php

namespace Mci\Bundle\UserBundle\Exception;

class UsernameNotFoundException extends \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
{
    /**
     * {@inheritDoc}
     */
    public function getMessageKey()
    {
        return 'Invalid username or password!';
    }
}
