<?php

namespace Tagcade\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Tagcade\Model\User\UserEntityInterface;

class User extends BaseUser implements UserEntityInterface
{
    // These properties are required to be redeclared by jms serializer
    // We excluded all fields in the parent class, in order to expose
    // a subset of the fields we need to overwrite that config.
    //
    // The only way to do it is to redeclare them here so that out new config works
    // See the serializer config in the Resources/config/serializer directory of this bundle

    protected $id;
    protected $username;
    protected $email;
    protected $enabled;
    protected $lastLogin;
    protected $roles;

    public function getEnabledFeatures()
    {
        return array_filter($this->getRoles(), function($role) {
            return strpos($role, 'FEATURE_') === 0;
        });
    }

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