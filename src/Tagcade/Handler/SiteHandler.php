<?php

namespace Tagcade\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\SiteInterface;
use Tagcade\Model\User\Role\RoleInterface;
use Tagcade\Exception\InvalidFormException;

abstract class SiteHandler implements SiteHandlerInterface
{
    private $om;
    private $entityClass;
    /**
     * @var \Tagcade\Repository\SiteRepository
     */
    private $repository;
    private $formFactory;
    private $userRole;

    /**
     * @param ObjectManager $om
     * @param $entityClass
     * @param FormFactoryInterface $formFactory
     * @param FormTypeInterface|string $formType
     * @param RoleInterface $role
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $formType, RoleInterface $role)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->formType = $formType;
        $this->setUserRole($role);
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->repository->find($id);
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
        $site = $this->createSite();

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

    public function getUserRole()
    {
        return $this->userRole;
    }

    protected function setUserRole(RoleInterface $user)
    {
        $this->userRole = $user;
    }

    protected function getRepository()
    {
        return $this->repository;
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

            $this->om->persist($site);
            $this->om->flush($site);

            return $site;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createSite()
    {
        return new $this->entityClass();
    }
}