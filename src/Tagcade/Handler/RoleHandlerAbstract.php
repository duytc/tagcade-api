<?php

namespace Tagcade\Handler;

use Tagcade\DomainManager\SiteManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\InvalidUserRoleException;
use Tagcade\Exception\InvalidFormException;

abstract class RoleHandlerAbstract implements RoleHandlerInterface
{
    private $formFactory;
    private $formType;
    protected $domainManager;
    private $userRole;

    public function __construct(
        FormFactoryInterface $formFactory,
        FormTypeInterface $formType,
        SiteManagerInterface $domainManager,
        UserRoleInterface $userRole = null)
    {
        $this->formFactory = $formFactory;
        $this->formType = $formType;
        $this->domainManager = $domainManager;

        if ($userRole) {
            $this->setUserRole($userRole);
        }
    }

    public function setUserRole(UserRoleInterface $userRole)
    {
        if (!$this->supportsRole($userRole)) {
            throw new InvalidUserRoleException();
        }

        $this->userRole = $userRole;
    }

    public function getUserRole()
    {
        if (!$this->userRole instanceof UserRoleInterface) {
            throw new LogicException('userRole is not set');
        }

        return $this->userRole;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->domainManager->find($id);
    }

    /**
     * @inheritdoc
     */
    abstract public function all($limit = 5, $offset = 0);

    /**
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        $entity = $this->domainManager->createNew();

        return $this->processForm($entity, $parameters, 'POST');
    }

    /**
     * @inheritdoc
     */
    public function put(ModelInterface $entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PUT');
    }

    /**
     * @inheritdoc
     */
    public function patch(ModelInterface $entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param ModelInterface $entity
     * @param array $parameters
     * @param String $method
     *
     * @return ModelInterface
     *
     * @throws InvalidFormException
     */
    protected function processForm(ModelInterface $entity, array $parameters, $method = "PUT")
    {
        if (!$this->supportsEntity($entity)) {
            throw new LogicException(sprintf('%s is not supported by this handler', get_class($entity)));
        }

        $options = [
            'method' => $method,
            'current_user_role' => $this->getUserRole(),
        ];

        $form = $this->formFactory->create($this->formType, $entity, $options);

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $entity = $form->getData();

            $this->domainManager->save($entity);

            return $entity;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}