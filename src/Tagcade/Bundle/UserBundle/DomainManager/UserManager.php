<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserManagerInterface as FOSUserManagerInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use Rollerworks\Bundle\MultiUserBundle\Model\DelegatingUserManager;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

/**
 * Most of the other handlers talk to doctrine directly
 * This one is wrapping the bundle-specific FOSUserBundle
 * whilst keep a consistent API with the other handlers
 */
class UserManager implements UserManagerInterface
{
    const ROLE_PUBLISHER = 'ROLE_PUBLISHER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var DelegatingUserManager
     */
    protected $FOSUserManager;

    protected $userPublisherSystem;

    protected $userAdminSystem;

    protected $allUserSystems;

    protected $currentUserSystem;

    public function __construct(FOSUserManagerInterface $userManager, $userPublisherSystem, $userAdminSystem)
    {
        $this->FOSUserManager = $userManager;

        $this->userPublisherSystem = $userPublisherSystem;
        $this->userAdminSystem = $userAdminSystem;

        $this->allUserSystems[] = $this->userAdminSystem;
        $this->allUserSystems[] = $this->userPublisherSystem;

        $this->currentUserSystem = $this->FOSUserManager->getUserDiscriminator()->getCurrentUser();

        if (null === $this->currentUserSystem) {
            $this->currentUserSystem = $userPublisherSystem;
        }

        if (!in_array($this->currentUserSystem, $this->allUserSystems)) {
            throw new LogicException( sprintf('current user system %s is not configured yet', $this->currentUserSystem));
        }

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
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->userPublisherSystem);
        $this->FOSUserManager->updateUser($user);
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->currentUserSystem);
    }

    /**
     * @inheritdoc
     */
    public function delete(FOSUserInterface $user)
    {
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->userPublisherSystem);
        $this->FOSUserManager->deleteUser($user);
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->currentUserSystem);
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->userPublisherSystem);
        $entity = $this->FOSUserManager->createUser();
        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->currentUserSystem);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        $user = null;

        foreach ($this->allUserSystems as $userSystem) {
            $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($userSystem);
            $user = $this->FOSUserManager->findUserBy(['id' => $id]);

            if (null !== $user) {
                break;
            }
        }

        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->currentUserSystem);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        $users = array();

        array_walk(
            $this->allUserSystems,
            function($userSystem) use (&$users)
            {
                $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($userSystem);
                $users = array_merge($users, $this->FOSUserManager->findUsers());
            }
        );

        $this->FOSUserManager->getUserDiscriminator()->setCurrentUser($this->currentUserSystem);

        return $users;
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
    public function findPublisher($id)
    {
        $publisher = $this->find($id);

        if (!$publisher) {
            return false;
        }

        if (!$publisher instanceof PublisherInterface) {
            return false;
        }

        return $publisher;
    }
}
