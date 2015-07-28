<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryAdTagRepositoryInterface;

class LibraryAdTagManager implements LibraryAdTagManagerInterface
{
    protected $em;
    protected $repository;

    public function __construct(EntityManagerInterface $em, LibraryAdTagRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryAdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(LibraryAdTagInterface $libraryAdTag)
    {
        $libraryAdTagId = $libraryAdTag->getId();

        if($libraryAdTagId == null){

            $this->em->persist($libraryAdTag);
            $this->em->flush();

        }
    }

    /**
     * @inheritdoc
     */
    public function delete(LibraryAdTagInterface $libraryAdTag)
    {
        $this->em->remove($libraryAdTag);
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
        return $this->repository->findBy($criteria = ['visible'=>true], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getLibraryAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryAdTagsForPublisher($publisher, $limit, $offset);
    }
}