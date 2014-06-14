<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Model\SiteInterface;

class PublisherSiteHandler extends SiteHandlerAbstract
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
        return $this->domainManager->getSitesForPublisher($this->getUserRole(), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    protected function processForm(ModelInterface $site, array $parameters, $method = "PUT")
    {
        /** @var SiteInterface $site */
        if (null == $site->getPublisher()) {
            $site->setPublisher($this->getUserRole());
        }

        return parent::processForm($site, $parameters, $method);
    }
}