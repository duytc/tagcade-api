<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\DisplayBlacklistHandlerAbstract;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DisplayBlacklistHandler extends DisplayBlacklistHandlerAbstract
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
        return $this->getDomainManager()->getDisplayBlacklistsForPublisher($this->getUserRole(), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $blacklist, array $parameters, $method = "PUT")
    {
        /** @var DisplayBlacklistInterface $blacklist*/
        if (null == $blacklist->getPublisher()) {
            $blacklist->setPublisher($this->getUserRole());
        }

        return parent::processForm($blacklist, $parameters, $method);
    }
}