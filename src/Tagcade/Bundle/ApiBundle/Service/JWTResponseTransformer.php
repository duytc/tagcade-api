<?php

namespace Tagcade\Bundle\ApiBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;

class JWTResponseTransformer
{
    public function transform(array $data, UserInterface $user)
    {
        $data['username'] = $user->getUsername();
        $data['roles'] = $user->getRoles();

        return $data;
    }
}