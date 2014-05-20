<?php

namespace Tagcade\Model\User\Role;

class Admin extends AbstractRole
{
    protected static $requiredRoles = ['ROLE_ADMIN'];
}
