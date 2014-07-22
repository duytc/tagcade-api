<?php

namespace Tagcade\Bundle\AdminApiBundle\Handler;

use Tagcade\Handler\HandlerAbstract;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;

/**
 * Not using a RoleHandlerInterface because this Handler is local
 * to the admin api bundle. All routes to this bundle are protected in app/config/security.yml
 */
class UserHandler extends HandlerAbstract
{
    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, UserManagerInterface $domainManager)
    {
        parent::__construct($formFactory, $formType, $domainManager);
    }
}