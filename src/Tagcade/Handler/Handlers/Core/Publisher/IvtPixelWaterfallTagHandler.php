<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\IvtPixelWaterfallTagHandlerAbstract;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class IvtPixelWaterfallTagHandler extends IvtPixelWaterfallTagHandlerAbstract
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
    public function getUserRole()
    {
        $role = parent::getUserRole();

        if (!$role instanceof PublisherInterface) {
            throw new LogicException('userRole does not implement PublisherInterface');
        }

        return $role;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->getIvtPixelWaterfallTagsForPublisher($this->getUserRole(), $limit, $offset);
    }
}