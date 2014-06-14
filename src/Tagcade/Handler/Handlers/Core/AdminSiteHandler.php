<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\AdminInterface;

class AdminSiteHandler extends SiteHandlerAbstract
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
        return $this->domainManager->all($limit, $offset);
    }
}