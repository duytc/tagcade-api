<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;

class LibraryDynamicAdSlotManager implements LibraryDynamicAdSlotManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, LibraryDynamicAdSlotRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryDynamicAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(LibraryDynamicAdSlotInterface $adSlot)
    {
        $this->om->persist($adSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(LibraryDynamicAdSlotInterface $adSlot)
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
     * @inheritdoc
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryDynamicAdSlotsForPublisher($publisher, $limit, $offset);
    }
}