<?php

namespace Mci\Bundle\UserBundle\Exception;

class DisabledException extends \Symfony\Component\Security\Core\Exception\DisabledException
{
    /**
     * {@inheritDoc}
     */
    public function getMessageKey()
    {
        return 'Account is disabled!';
    }
}
