<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\LogicException;
use Tagcade\Handler\Handlers\Core\BillingConfigurationHandlerAbstract;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class BillingConfigurationHandler extends BillingConfigurationHandlerAbstract
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
        return $this->getDomainManager()->getAllConfigurationForPublisher($this->getUserRole());
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $billingConfiguration, array $parameters, $method = "PUT")
    {
        /** @var BillingConfigurationInterface $billingConfiguration */
        if (null == $billingConfiguration->getPublisher()) {
            $billingConfiguration->setPublisher($this->getUserRole());
        }

        return parent::processForm($billingConfiguration, $parameters, $method);
    }
}