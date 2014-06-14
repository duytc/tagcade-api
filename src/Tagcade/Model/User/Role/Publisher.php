<?php

namespace Tagcade\Model\User\Role;

class Publisher extends UserRoleAbstract implements PublisherInterface
{
    protected static $requiredRoles = ['ROLE_PUBLISHER'];
}
