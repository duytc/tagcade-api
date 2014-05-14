<?php

namespace Tagcade\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Tagcade\Model\User\UserInterface as TagcadeUserInterface;
use Doctrine\ORM\Mapping as ORM;

class User extends BaseUser implements TagcadeUserInterface
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