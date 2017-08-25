<?php

namespace Tagcade\Handler\Handlers\Core\Admin;

use Tagcade\Handler\Handlers\Core\IvtPixelWaterfallTagHandlerAbstract;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class IvtPixelWaterfallTagHandler extends IvtPixelWaterfallTagHandlerAbstract
{
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AdminInterface;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->all($limit, $offset);
    }
}