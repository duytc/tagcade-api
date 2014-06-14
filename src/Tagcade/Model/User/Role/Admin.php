<?php

namespace Tagcade\Model\User\Role;

class Admin extends UserRoleAbstract implements AdminInterface
{
    protected static $requiredRoles = ['ROLE_ADMIN'];
}
