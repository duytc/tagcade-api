<?php

namespace Tagcade\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(name="email", column=@ORM\Column(nullable=true)),
 *     @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(nullable=true, unique=false))
 * })
 */
class User extends BaseUser
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