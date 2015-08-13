<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class LibrarySlotTagManager implements LibrarySlotTagManagerInterface
{
    protected $em;
    protected $repository;
    /**
     * @var ReplicatorInterface
     */
    protected $replicator;

    public function __construct(EntityManagerInterface $em, LibrarySlotTagRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function setReplicator(ReplicatorInterface $replicator) {
        $this->replicator = $replicator;
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
    public function save(ModelInterface $librarySlotTag)
    {
        if(!$librarySlotTag instanceof LibrarySlotTagInterface) throw new InvalidArgumentException('expect LibrarySlotTagInterface object');

        $this->em->persist($librarySlotTag);

        if($librarySlotTag->getId() === null) {
            $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);
        } else {
            $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);
        }

        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $librarySlotTag)
    {
        if(!$librarySlotTag instanceof LibrarySlotTagInterface) throw new InvalidArgumentException('expect LibrarySlotTagInterface object');

        $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag, true);
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