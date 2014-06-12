<?php

namespace Tagcade\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Model\User\Role\RoleInterface;

Class RoleToUserEntityTransformer implements DataTransformerInterface
{
    public function transform($role)
    {
        if (null === $role) {
            return null;
        }

        if ($role instanceof RoleInterface) {
            return $role->getUser();
        }

        return $role;
    }

    public function reverseTransform($user)
    {
        return new Publisher($user);
    }
}