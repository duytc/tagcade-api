<?php

namespace Tagcade\Model\User\Role;

class Publisher extends RoleAbstract implements PublisherInterface
{
    protected static $requiredRoles = ['ROLE_PUBLISHER'];
}
