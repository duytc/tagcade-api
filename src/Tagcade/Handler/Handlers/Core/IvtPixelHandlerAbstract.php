<?php

namespace Tagcade\Handler\Handlers\Core;

use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Form\Type\RoleSpecificFormTypeInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\User\Role\UserRoleInterface;

abstract class IvtPixelHandlerAbstract extends RoleHandlerAbstract
{
    public function __construct(FormFactoryInterface $formFactory, RoleSpecificFormTypeInterface $formType, $domainManager, UserRoleInterface $userRole = null)
    {
        parent::__construct($formFactory, $formType, $domainManager, $userRole);
    }

    /**
     * @inheritdoc
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}