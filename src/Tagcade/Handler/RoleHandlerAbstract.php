<?php

namespace Tagcade\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\InvalidUserRoleException;

/**
 * A role handler is used to have a different handler for different user roles.
 *
 * i.e you may wish an Admin to be able to edit all entities
 * however a Publisher can only edit their entities
 */
abstract class RoleHandlerAbstract extends HandlerAbstract implements RoleHandlerInterface
{
    protected $userRole;

    public function __construct(FormFactoryInterface $formFactory, FormTypeInterface $formType, $domainManager, UserRoleInterface $userRole = null)
    {
        parent::__construct($formFactory, $formType, $domainManager);

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
    protected function processForm(ModelInterface $entity, array $parameters, $method = 'PUT', $extraFormOptions = [])
    {
        $extraFormOptions = array_merge(
            ['user_role' => $this->getUserRole()],
            $extraFormOptions
        );

        return parent::processForm($entity, $parameters, $method, $extraFormOptions);
    }
}