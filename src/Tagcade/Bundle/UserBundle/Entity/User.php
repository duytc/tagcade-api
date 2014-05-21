<?php

namespace Tagcade\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Tagcade\Model\User\UserEntityInterface;

class User extends BaseUser implements UserEntityInterface
{
    public function setEmail($email)
    {
        if (empty($email)) {
            $email = null;
        }

        $this->email = $email;

        return $this;
    }

    public function setEmailCanonical($emailCanonical)
    {
        if (empty($emailCanonical)) {
            $emailCanonical = null;
        }

        $this->emailCanonical = $emailCanonical;

        return $this;
    }
}