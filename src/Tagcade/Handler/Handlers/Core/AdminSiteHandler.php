<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\SiteManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\AdminInterface;

class AdminSiteHandler extends SiteHandlerAbstract
{
    /**
     * @inheritdoc
     * @param AdminInterface|null $admin
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormTypeInterface $formType,
        SiteManagerInterface $domainManager,
        AdminInterface $admin = null
    )
    {
        parent::__construct($formFactory, $formType, $domainManager, $admin);
    }

    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof AdminInterface;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->domainManager->all($limit, $offset);
    }
}