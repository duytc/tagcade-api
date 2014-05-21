<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\User\UserEntityInterface;

interface RoleInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getUser();
}