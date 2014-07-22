<?php

namespace Tagcade\Handler\Handlers\Core\Admin;

use Tagcade\Handler\Handlers\Core\SiteHandlerAbstract;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\AdminInterface;

class SiteHandler extends SiteHandlerAbstract
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