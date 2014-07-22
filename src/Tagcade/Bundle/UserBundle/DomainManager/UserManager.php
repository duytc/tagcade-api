<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserManagerInterface as FOSUserManagerInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class UserManager implements UserManagerInterface
{
    protected $FOSUserManager;

    public function __construct(FOSUserManagerInterface $userManager)
    {
        $this->FOSUserManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, FOSUserInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(FOSUserInterface $user)
    {
        $this->FOSUserManager->updateUser($user);
    }

    /**
     * @inheritdoc
     */
    public function delete(FOSUserInterface $user)
    {
        $this->FOSUserManager->deleteUser($user);
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        return $this->FOSUserManager->createUser();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->FOSUserManager->findUserBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->FOSUserManager->findUsers();
    }
}
