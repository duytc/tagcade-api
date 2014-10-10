<?php

namespace Tagcade\Bundle\UserBundle\DomainManager;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface UserManagerInterface
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
     * @return FOSUserInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return FOSUserInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return FOSUserInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @return array
     */
    public function allPublishers();

    /**
     * @return array
     */
    public function allPublisherRoles();
}