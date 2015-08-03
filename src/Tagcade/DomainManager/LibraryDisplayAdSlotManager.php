<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

class LibraryDisplayAdSlotManager implements LibraryDisplayAdSlotManagerInterface
{
    protected $om;
    protected $repository;
    protected $librarySlotTagRepository;

    public function __construct(ObjectManager $om, LibraryDisplayAdSlotRepositoryInterface $repository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->librarySlotTagRepository = $librarySlotTagRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryDisplayAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(LibraryDisplayAdSlotInterface $adSlot)
    {
        $this->om->persist($adSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(LibraryDisplayAdSlotInterface $adSlot)
    {
        $this->om->remove($adSlot);
        $this->om->flush();
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
        $criteria = array(
            'visible' => true
        );

        return $this->repository->findBy($criteria, $orderBy = null, $limit, $offset);
    }


    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }
}