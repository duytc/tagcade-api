<?php

namespace Tagcade\Model\User;

use Tagcade\Model\ModelInterface;

interface UserEntityInterface extends ModelInterface
{
    public function getId();

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * @return []
     */
    public function getRoles();

    public function hasRole($role);
}