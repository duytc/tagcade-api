<?php

namespace Tagcade\DomainManager;

/**
 * A dummy class since PHP does not support generics
 */

use Tagcade\Model\ModelInterface;

interface ManagerInterface
{
    /**
     * @param ModelInterface $entity
     * @return void
     */
    public function save(ModelInterface $entity);

    /**
     * @param ModelInterface $entity
     * @return void
     */
    public function delete(ModelInterface $entity);

    /**
     * @return ModelInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return ModelInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return ModelInterface[]
     */
    public function all($limit = null, $offset = null);
}