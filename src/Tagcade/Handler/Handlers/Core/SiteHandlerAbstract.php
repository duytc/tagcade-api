<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;
use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\SiteInterface;

abstract class SiteHandlerAbstract extends RoleHandlerAbstract
{
    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, SiteManagerInterface $domainManager, UserRoleInterface $userRole = null)
    {
        parent::__construct($formFactory, $formType, $domainManager, $userRole);
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SiteInterface::class);
    }

    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SiteManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}