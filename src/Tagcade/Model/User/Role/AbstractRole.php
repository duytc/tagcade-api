<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\User\UserEntityInterface;
use InvalidArgumentException;

abstract class AbstractRole
{
    protected static $requiredRoles = [];
    private $user;

    public function __construct(UserEntityInterface $user)
    {
        if (!empty(static::$requiredRoles)) {
            $missingRoles = array_diff(static::$requiredRoles, $user->getRoles());

            if (!empty($missingRoles)) {
                throw new InvalidArgumentException(sprintf('user is missing required roles: %s', join(' ,', $missingRoles)));
            }
        }

        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
