<?php

namespace Tagcade\Handler\Handlers\Core\Admin;

use Tagcade\Handler\Handlers\Core\RonAdSlotHandlerAbstract;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class RonAdSlotHandler extends RonAdSlotHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AdminInterface;
    }
}