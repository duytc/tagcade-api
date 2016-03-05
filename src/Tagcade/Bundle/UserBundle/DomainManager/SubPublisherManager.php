<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class SubPublisherManager implements SubPublisherManagerInterface
{
    const ROLE_SUB_PUBLISHER = 'ROLE_SUB_PUBLISHER';

    /**
     * @var UserManagerInterface
     */
    protected $FOSUserManager;

    public function __construct(UserManagerInterface $userManager)
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
    public function all($limit = null, $offset = null)
    {
        /** @var array $publishers */
        $publishers = $this->FOSUserManager->findUsers();

        $publishers = array_filter($publishers, function (UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_SUB_PUBLISHER);
        });

        return array_values($publishers);
    }

    /**
     * @inheritdoc
     */
    public function allActive($limit = null, $offset = null)
    {
        $publishers = array_filter($this->all($limit, $offset), function (UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_SUB_PUBLISHER) && $user->isEnabled();
        });

        return array_values($publishers);
    }


    /**
     * @inheritdoc
     */
    public function find($id)
    {
        $publisher = $this->FOSUserManager->findUserBy(['id' => $id]);

        return (!$publisher instanceof PublisherInterface) ? false : $publisher;
    }

    /**
     * @inheritdoc
     */
    public function findByUsernameOrEmail($usernameOrEmail)
    {
        return $this->FOSUserManager->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @inheritdoc
     */
    public function update(UserInterface $token)
    {
        $this->FOSUserManager->updateUser($token);
    }

    /**
     * @inheritdoc
     */
    public function findByConfirmationToken($token)
    {
        return $this->FOSUserManager->findUserByConfirmationToken($token);
    }
}
