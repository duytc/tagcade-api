<?php

namespace Tagcade\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Model\User\Role\UserRoleInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Tagcade\Exception\InvalidUserRoleException;

Class RoleToUserEntityTransformer implements DataTransformerInterface
{
    public function transform($role)
    {
        if (!$role) {
            return null;
        }

        if ($role instanceof UserRoleInterface) {
            return $role->getUser();
        }

        return $role;
    }

    public function reverseTransform($user)
    {
        if (!$user) {
            return null;
        }

        try {
            return new Publisher($user);
        }
        catch (InvalidUserRoleException $e) {
            throw new TransformationFailedException(sprintf(
                'The user could not be converted to a publisher role'
            ));
        }
    }
}