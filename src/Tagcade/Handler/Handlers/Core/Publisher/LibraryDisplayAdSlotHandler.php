<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Handler\Handlers\Core\LibraryAdSlotHandlerAbstract;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryDisplayAdSlotHandler extends LibraryAdSlotHandlerAbstract
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
        /** @var PublisherInterface $publisher */
        $publisher = $this->getUserRole();
        return $this->getDomainManager()->getLibraryDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }
}