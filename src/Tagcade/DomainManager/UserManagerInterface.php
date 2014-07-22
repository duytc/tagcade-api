<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\User\UserEntityInterface;

interface UserManagerInterface
{
    /**
     * @param UserEntityInterface $user
     * @return void
     */
    public function save(UserEntityInterface $user);

    /**
     * @param UserEntityInterface $user
     * @return void
     */
    public function delete(UserEntityInterface $user);

    /**
     * @return UserEntityInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return UserEntityInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return UserEntityInterface[]
     */
    public function all($limit = null, $offset = null);
}