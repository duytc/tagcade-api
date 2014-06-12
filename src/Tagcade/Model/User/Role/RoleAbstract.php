<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Exception\InvalidUserRoleException;

abstract class RoleAbstract
{
    protected static $requiredRoles = [];
    private $user;

    public function __construct(UserEntityInterface $user)
    {
        if (!empty(static::$requiredRoles)) {
            $missingRoles = array_diff(static::$requiredRoles, $user->getRoles());

            if (!empty($missingRoles)) {
                throw new InvalidUserRoleException(sprintf('user is missing required roles: %s', join(' ,', $missingRoles)));
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
        if (null == $this->getUser()) {
            return null;
        }

        return $this->getUser()->getId();
    }
}
