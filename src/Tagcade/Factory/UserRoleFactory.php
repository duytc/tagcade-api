<?php

namespace Tagcade\Factory;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\User\Role\SuperAdmin;
use Tagcade\Model\User\Role\Admin;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Model\User\Role\PublisherSubAccount;;
use Tagcade\Exception\InvalidUserRoleException;
use Tagcade\Exception\InvalidArgumentException;

class UserRoleFactory implements UserRoleFactoryInterface
{
    /**
     * @inheritdoc
     */
    public static function getRole(UserEntityInterface $user = null)
    {
        if (null == $user) {
            throw new InvalidArgumentException('user is not set');
        }

        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            return new SuperAdmin($user);
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return new Admin($user);
        }

        if ($user->hasRole('ROLE_PUBLISHER')) {
            return new Publisher($user);
        }

        if ($user->hasRole('ROLE_PUBLISHER_SUB_ACCOUNT')) {
            return new PublisherSubAccount($user);
        }

        throw new InvalidUserRoleException('user does not have a valid role');
    }
}