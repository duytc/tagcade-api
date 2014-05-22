<?php

namespace Tagcade\Factory;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\User\Role\SuperAdmin;
use Tagcade\Model\User\Role\Admin;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Model\User\Role\PublisherSubAccount;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Exception\InvalidUserRoleException;
use \InvalidArgumentException;

/**
 * Converts a user entity with roles to our model classes
 */
class UserFactory
{
    /**
     * @param UserEntityInterface $user
     * @return \Tagcade\Model\User\Role\RoleInterface
     * @throws \Tagcade\Exception\InvalidUserRoleException
     * @throws \InvalidArgumentException
     */
    public static function getRole(UserEntityInterface $user = null)
    {
        if (null == $user) {
            throw new InvalidArgumentException('user is not set');
        }

        try {
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
        } catch(\Exception $e) {
            throw new InvalidUserRoleException($e->getMessage(), $e->getCode(), $e);
        }

        throw new InvalidUserRoleException('user does not have a valid role');
    }

    /**
     * @param UserEntityInterface $user
     * @return Publisher
     * @throws \Tagcade\Exception\InvalidUserRoleException
     */
    public static function getPublisher(UserEntityInterface $user = null)
    {
        $role = static::getRole($user);

        if (!$role instanceof PublisherInterface)
        {
            throw new InvalidUserRoleException('user is not a publisher');
        }

        return $role;
    }
}