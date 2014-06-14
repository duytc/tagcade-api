<?php

namespace Tagcade\Handler;

use Tagcade\Exception\NoHandlerForRoleException;
use Tagcade\Model\User\Role\UserRoleInterface;

class HandlerManager
{
    /**
     * @var RoleHandlerInterface[]
     */
    protected $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = [];

        foreach($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    public function addHandler(RoleHandlerInterface $handler)
    {
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