<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\SiteManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\SiteInterface;
use Tagcade\Exception\LogicException;

class PublisherSiteHandler extends SiteHandlerAbstract
{
    /**
     * @inheritdoc
     * @param PublisherInterface|null $admin
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormTypeInterface $formType,
        SiteManagerInterface $domainManager,
        PublisherInterface $publisher = null
    )
    {
        parent::__construct($formFactory, $formType, $domainManager, $publisher);
    }

    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof PublisherInterface;
    }

    /**
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
    protected function processForm(SiteInterface $site, array $parameters, $method = "PUT")
    {
        if (null == $site->getPublisher()) {
            $site->setPublisher($this->getUserRole());
        }

        return parent::processForm($site, $parameters, $method);
    }
}