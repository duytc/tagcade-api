<?php

namespace Tagcade\Model\User;

use InvalidArgumentException;

abstract class UserType
{
    protected static $requiredRoles = [];
    private $user;

    public function __construct(UserInterface $user)
    {
        if (!empty(static::$requiredRoles)) {
            $missingRoles = array_diff(static::$requiredRoles, $user->getRoles());

            if (!empty($missingRoles)) {
                throw new InvalidArgumentException(sprintf('user must is missing required roles: %s', join(' ,', $missingRoles)));
            }
        }

        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getId()
    {
        return $this->user->getId();
    }
}
