<?php

namespace Tagcade\Factory;

use Tagcade\Model\User\UserEntityInterface;
use \InvalidArgumentException;

/**
 * Converts a user entity with roles to our model classes
 */
interface UserRoleFactoryInterface
{
    /**
     * @param UserEntityInterface $user
     * @return \Tagcade\Model\User\Role\RoleInterface
     * @throws \Tagcade\Exception\InvalidUserRoleException
     * @throws \InvalidArgumentException
     */
    public static function getRole(UserEntityInterface $user = null);
}