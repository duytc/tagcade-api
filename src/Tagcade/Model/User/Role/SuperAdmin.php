<?php

namespace Tagcade\Model\User\Role;

class SuperAdmin extends AbstractRole
{
    protected static $requiredRoles = ['ROLE_SUPER_ADMIN'];
}
