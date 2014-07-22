<?php

namespace Tagcade\Handler\Handlers\Admin;

use Tagcade\DomainManager\UserManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\UserEntityInterface;

class UserHandler extends AdminHandlerAbstract
{
    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, UserManagerInterface $domainManager, UserRoleInterface $userRole = null)
    {
        parent::__construct($formFactory, $formType, $domainManager, $userRole);
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, UserEntityInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->getDomainManager()->all($limit, $offset);
    }

    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return UserManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}