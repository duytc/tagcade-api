<?php

namespace Tagcade\Model\User\Role;

class Publisher extends AbstractRole implements PublisherInterface
{
    protected static $requiredRoles = ['ROLE_PUBLISHER'];
}
