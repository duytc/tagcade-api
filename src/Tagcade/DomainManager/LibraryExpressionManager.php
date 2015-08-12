<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

class LibraryExpressionManager implements LibraryExpressionManagerInterface
{
    protected $em;
    protected $repository;

    public function __construct(EntityManagerInterface $em, LibraryExpressionRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryExpressionInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(LibraryExpressionInterface $libraryExpression)
    {
        $libraryExpressionId = $libraryExpression->getId();

        if($libraryExpressionId == null){

            $this->em->persist($libraryExpression);
            $this->em->flush();

        }
    }

    /**
     * @inheritdoc
     */
    public function delete(LibraryExpressionInterface $libraryExpression)
    {
        $this->em->remove($libraryExpression);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }
}