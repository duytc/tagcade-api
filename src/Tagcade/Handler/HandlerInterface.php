<?php

namespace Tagcade\Handler;

use Tagcade\Model\ModelInterface;

interface HandlerInterface
{
    /**
     * Should take an object instance or string class name
     *
     * @param ModelInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * Get a Entity.
     *
     * @param mixed $id
     *
     * @return ModelInterface
     */
    public function get($id);

    /**
     * Get a list of Entities.
     *
     * @param int $limit the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Create a new Entity.
     *
     * @param array $parameters
     *
     * @return ModelInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Entity.
     *
     * @param ModelInterface $entity
     * @param array $parameters
     *
     * @return ModelInterface
     */
    public function put(ModelInterface $entity, array $parameters);

    /**
     * Partially update a Entity.
     *
     * @param ModelInterface $entity
     * @param array $parameters
     *
     * @return ModelInterface
     */
    public function patch(ModelInterface $entity, array $parameters);
}