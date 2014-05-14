<?php

namespace Tagcade\Model\User;

class PublisherSubAccount extends UserType
{
    protected static $requiredRoles = ['ROLE_PUBLISHER_SUB_ACCOUNT'];
}
