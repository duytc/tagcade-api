<?php

namespace Tagcade\Handler;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\Handler\NoHandlerForRoleException;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\ModelInterface;
use ReflectionClass;

/**
 * Returns a resource handler for the current user role
 * if available
 */
class HandlerManager
{
    protected $entityClass;

    /**
     * @var RoleHandlerInterface[]
     */
    protected $handlers;

    public function __construct($entityClass, array $handlers)
    {
        $entityRef = new ReflectionClass($entityClass);

        if (!$entityRef->isInstantiable() || !$entityRef->implementsInterface(ModelInterface::class)) {
            throw new InvalidArgumentException(sprintf('entity class must be instantiable and implement %s', ModelInterface::class));
        }

        $this->entityClass = $entityClass;
        $this->handlers = [];

        foreach($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    public function addHandler(RoleHandlerInterface $handler)
    {
        if (!$handler->supportsEntity($this->entityClass)) {
            throw new InvalidArgumentException(sprintf('handler must support the entity %s', $this->entityClass));
        }

        $this->handlers[] = $handler;
    }

    /**
     * @param UserRoleInterface $role
     * @return RoleHandlerInterface
     * @throws NoHandlerForRoleException
     */
    public function getHandler(UserRoleInterface $role)
    {
        foreach($this->handlers as $handler) {
            if ($handler->supportsRole($role)) {
                $handler = clone $handler;
                $handler->setUserRole($role);

                return $handler;
            }
        }

        throw new NoHandlerForRoleException();
    }
}