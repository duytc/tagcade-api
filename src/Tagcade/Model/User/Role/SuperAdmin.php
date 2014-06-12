<?php

namespace Tagcade\Model\User\Role;

class SuperAdmin extends RoleAbstract implements AdminInterface
{
    protected static $requiredRoles = ['ROLE_SUPER_ADMIN'];
}
