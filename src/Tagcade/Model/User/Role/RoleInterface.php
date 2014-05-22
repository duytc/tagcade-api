<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\User\UserEntityInterface;

interface RoleInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getUser();

    /**
     * @return int|null
     */
    public function getId();
}