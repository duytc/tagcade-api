<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryExpressionManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibraryExpressionInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return void
     */
    public function save(LibraryExpressionInterface $libraryExpression);

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return void
     */
    public function delete(LibraryExpressionInterface $libraryExpression);

    /**
     * @return LibraryExpressionInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibraryExpressionInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryExpressionInterface[]
     */
    public function all($limit = null, $offset = null);
}