<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\ReplicateLibraryAdSlotDataTrait;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

class LibrarySlotTagManager implements LibrarySlotTagManagerInterface
{
    use ReplicateLibraryAdSlotDataTrait;

    protected $em;
    protected $repository;


    public function __construct(EntityManagerInterface $em, LibrarySlotTagRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibrarySlotTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(LibrarySlotTagInterface $librarySlotTag)
    {
        $this->em->persist($librarySlotTag);

        if($librarySlotTag->getId() === null) {
            $this->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);
        } else {
            $this->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);
        }

        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(LibrarySlotTagInterface $librarySlotTag)
    {
        $this->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag, true);
        $this->em->remove($librarySlotTag);
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
        $criteria = [];
        return $this->repository->findBy($criteria, $orderBy = null, $limit, $offset);
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        return $this->repository->getByLibraryAdSlot($libraryAdSlot, $limit, $offset);
    }

    public function getByLibraryAdSlotAndLibraryAdTagAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, LibraryAdTagInterface $libraryAdTag, $refId)
    {
        return $this->repository->getByLibraryAdSlotAndLibraryAdTagAndRefId($libraryAdSlot, $libraryAdTag, $refId);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }


}