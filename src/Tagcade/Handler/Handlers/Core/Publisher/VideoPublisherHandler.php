<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\VideoPublisherHandlerAbstract;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class VideoPublisherHandler extends VideoPublisherHandlerAbstract
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

    protected function processForm(ModelInterface $entity, array $parameters, $method = 'PUT')
    {
        /** @var VideoPublisherInterface $entity */
        if (null == $entity->getPublisher()) {
            $entity->setPublisher($this->getUserRole());
        }

        return parent::processForm($entity, $parameters, $method);
    }


    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->getVideoPublishersForPublisher($this->getUserRole(), $limit, $offset);
    }
}