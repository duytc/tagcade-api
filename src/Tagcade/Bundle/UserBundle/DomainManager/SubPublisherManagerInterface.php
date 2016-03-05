<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserInterface;
use Tagcade\Exception\InvalidUserRoleException;
use Tagcade\Model\User\Role\PublisherInterface;

interface SubPublisherManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param FOSUserInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param FOSUserInterface $user
     * @return void
     */
    public function save(FOSUserInterface $user);

    /**
     * @param FOSUserInterface $user
     * @return void
     */
    public function delete(FOSUserInterface $user);

    /**
     * Create new Publisher only
     * @return FOSUserInterface
     */
    public function createNew();

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return FOSUserInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function allActive($limit = null, $offset = null);

    /**
     * @param int $id
     * @return PublisherInterface|bool
     * @throws InvalidUserRoleException
     */
    public function find($id);

    /**
     * Finds a user by its username or email.
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface or null if user does not exist
     */
    public function findByUsernameOrEmail($usernameOrEmail);

    /**
     * Updates a user.
     *
     * @param UserInterface $token
     *
     * @return void
     */
    public function update(UserInterface $token);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface or null if user does not exist
     */
    public function findByConfirmationToken($token);
}