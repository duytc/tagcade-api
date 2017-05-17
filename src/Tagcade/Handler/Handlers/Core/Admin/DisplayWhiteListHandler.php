<?php

namespace Tagcade\Handler\Handlers\Core\Admin;

use Tagcade\Handler\Handlers\Core\DisplayWhiteListHandlerAbstract;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DisplayWhiteListHandler extends DisplayWhiteListHandlerAbstract
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