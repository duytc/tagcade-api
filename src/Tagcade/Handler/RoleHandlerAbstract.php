<?php

namespace Tagcade\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\InvalidUserRoleException;
use Tagcade\Exception\InvalidFormException;

abstract class RoleHandlerAbstract implements RoleHandlerInterface
{
    protected $formFactory;

    protected $formType;

    /**
     * @var \Tagcade\DomainManager\ManagerInterface
     *
     * We are using the dummy class above only for IDE completion.
     *
     * We had to do this because PHP does not support generics and we wanted to type hint
     * our domain managers/models.
     *
     * At a minimum it should support the methods in the dummy class above.
     *
     * The existence of the required methods will be checked in setDomainManager of this class
     */
    protected $domainManager;

    protected $userRole;

    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, $domainManager, UserRoleInterface $userRole = null)
    {
        $this->formFactory = $formFactory;
        $this->formType = $formType;
        $this->setDomainManager($domainManager);

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
            'user_role' => $this->getUserRole(),
        ];

        $form = $this->formFactory->create($this->formType, $entity, $options);

        $formConfig = $form->getConfig();

        if (!is_a($entity, $formConfig->getDataClass())) {
            throw new LogicException(sprintf('Form data class does not match entity returned from domain manager'));
        }

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $entity = $form->getData();

            $this->domainManager->save($entity);

            return $entity;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Returns the domain manager object
     * PHP's lack of generics support requires this, see comments at top of class
     *
     * @return \Tagcade\DomainManager\ManagerInterface
     */
    protected function getDomainManager()
    {
        return $this->domainManager;
    }

    private function setDomainManager($domainManager)
    {
        // todo check object methods
        $this->domainManager = $domainManager;
    }
}