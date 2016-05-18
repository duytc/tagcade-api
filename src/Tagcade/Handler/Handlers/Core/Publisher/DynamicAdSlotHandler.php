<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Handler\Handlers\Core\DynamicAdSlotHandlerAbstract;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DynamicAdSlotHandler extends DynamicAdSlotHandlerAbstract
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
        return $this->getDomainManager()->getDynamicAdSlotsForPublisher($publisher, $limit, $offset);
    }
}