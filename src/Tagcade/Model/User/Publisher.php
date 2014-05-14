<?php

namespace Tagcade\Model\User;

class Publisher extends UserType
{
    protected static $requiredRoles = ['ROLE_PUBLISHER'];
}
