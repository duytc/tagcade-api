<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\DisplayWhiteListHandlerAbstract;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DisplayWhiteListHandler extends DisplayWhiteListHandlerAbstract
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
        return $this->getDomainManager()->getDisplayWhiteListsForPublisher($this->getUserRole(), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $whiteList, array $parameters, $method = "PUT")
    {
        /** @var DisplayWhiteListInterface $whiteList */
        if (null == $whiteList->getPublisher()) {
            $whiteList->setPublisher($this->getUserRole());
        }

        return parent::processForm($whiteList, $parameters, $method);
    }
}