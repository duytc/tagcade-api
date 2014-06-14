<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\SiteManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Exception\InvalidUserRoleException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\SiteInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Exception\InvalidFormException;

abstract class SiteHandlerAbstract implements SiteHandlerInterface
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
    abstract public function all($limit = null, $offset = null);

    /**
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        $site = $this->domainManager->createNew();

        return $this->processForm($site, $parameters, 'POST');
    }

    /**
     * @inheritdoc
     */
    public function put(SiteInterface $site, array $parameters)
    {
        return $this->processForm($site, $parameters, 'PUT');
    }

    /**
     * @inheritdoc
     */
    public function patch(SiteInterface $site, array $parameters)
    {
        return $this->processForm($site, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param SiteInterface $site
     * @param array $parameters
     * @param String $method
     *
     * @return SiteInterface
     *
     * @throws InvalidFormException
     */
    protected function processForm(SiteInterface $site, array $parameters, $method = "PUT")
    {
        $options = [
            'method' => $method,
            'current_user_role' => $this->getUserRole(),
        ];

        $form = $this->formFactory->create($this->formType, $site, $options);

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $site = $form->getData();

            $this->domainManager->save($site);

            return $site;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}