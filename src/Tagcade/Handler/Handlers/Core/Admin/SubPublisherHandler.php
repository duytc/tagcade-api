<?php

namespace Tagcade\Handler\Handlers\Core\Admin;

use Tagcade\Handler\Handlers\Core\SubPublisherHandlerAbstract;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class SubPublisherHandler extends SubPublisherHandlerAbstract
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