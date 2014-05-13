<?php

namespace Tagcade\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tagcade\Bundle\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}