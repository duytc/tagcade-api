<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserManagerInterface as FOSUserManagerInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Factory\UserRoleFactory;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class UserManager implements UserManagerInterface
{
    const ROLE_PUBLISHER = 'ROLE_PUBLISHER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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

    /**
     * @inheritdoc
     */
    public function allPublishers()
    {
        $publishers = array_filter($this->all(), function(UserEntityInterface $user) {
            return $user->hasRole(static::ROLE_PUBLISHER);
        });

        return array_values($publishers);
    }

    /**
     * @inheritdoc
     */
    public function allPublisherRoles()
    {
        return array_map(function(UserEntityInterface $user) {
            return $this->getUserRole($user);
        }, $this->allPublishers());
    }

    public function getUserRole(UserEntityInterface $user)
    {
        return UserRoleFactory::getRole($user);
    }

    /**
     * @inheritdoc
     */
    public function findPublisher($id)
    {
        $publisher = $this->find($id);

        if (!$publisher) {
            return false;
        }

        try {
            $userRole = $this->getUserRole($publisher);
        }
        catch (\Exception $e) {
            return false;
        }

        if (!$userRole instanceof PublisherInterface) {
            return false;
        }

        return $userRole;
    }
}
