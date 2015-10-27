<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\SegmentHandlerAbstract;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class SegmentHandler extends SegmentHandlerAbstract
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
     * @return PublisherInterface
     * @throws LogicException
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
        return $this->getDomainManager()->getSegmentsForPublisher($this->getUserRole(), $limit, $offset);
    }
}