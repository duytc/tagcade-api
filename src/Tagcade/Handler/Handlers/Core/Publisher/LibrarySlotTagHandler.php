<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Handler\Handlers\Core\LibrarySlotTagHandlerAbstract;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibrarySlotTagHandler extends LibrarySlotTagHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof PublisherInterface;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->all($limit, $offset);
    }
}