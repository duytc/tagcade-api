<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\User\UserInterface;

interface RoleInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();
}