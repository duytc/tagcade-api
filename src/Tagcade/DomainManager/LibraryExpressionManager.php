<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\ModelInterface;
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
    public function save(ModelInterface $libraryExpression)
    {
        if(!$libraryExpression instanceof LibraryExpressionInterface) throw new InvalidArgumentException('expect LibraryExpressionInterface object');

        $libraryExpressionId = $libraryExpression->getId();

        if($libraryExpressionId == null){

            $this->em->persist($libraryExpression);
            $this->em->flush();

        }
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $libraryExpression)
    {
        if(!$libraryExpression instanceof LibraryExpressionInterface) throw new InvalidArgumentException('expect LibraryExpressionInterface object');

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