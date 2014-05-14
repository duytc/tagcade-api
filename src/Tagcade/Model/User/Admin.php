<?php

namespace Tagcade\Model\User;

class Admin extends UserType
{
    protected static $requiredRoles = ['ROLE_ADMIN'];
}
