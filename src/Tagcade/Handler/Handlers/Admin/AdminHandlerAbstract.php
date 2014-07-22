<?php

namespace Tagcade\Handler\Handlers\Admin;

use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\AdminInterface;

abstract class AdminHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AdminInterface;
    }
}