<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Handler\Handlers\Core\AdTagHandlerAbstract;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagHandler extends AdTagHandlerAbstract
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
        return $this->getDomainManager()->getAdTagsForPublisher($publisher, $limit, $offset);
    }
}