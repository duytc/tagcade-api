<?php

namespace Tagcade\Model\User\Role;

class Admin extends RoleAbstract implements AdminInterface
{
    protected static $requiredRoles = ['ROLE_ADMIN'];
}
