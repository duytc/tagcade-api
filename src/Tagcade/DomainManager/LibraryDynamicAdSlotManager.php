<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class LibraryDynamicAdSlotManager implements LibraryDynamicAdSlotManagerInterface
{
    protected $om;
    protected $repository;
    /**
     * @var ReplicatorInterface
     */
    private $replicator;

    public function __construct(ObjectManager $om, LibraryDynamicAdSlotRepositoryInterface $repository, ReplicatorInterface $replicator)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->replicator = $replicator;
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
    public function save(ModelInterface $entity)
    {
        if(!$entity instanceof LibraryDynamicAdSlotInterface) throw new InvalidArgumentException('expect LibraryDynamicAdSlotInterface object');

        $this->om->persist($entity);
        // creating default ad slot and expression for all referencing slots if the LibraryDynamicAdSlot introduces libraryExpression
        $this->replicator->replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots($entity);

        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryDynamicAdSlotInterface) throw new InvalidArgumentException('expect LibraryDynamicAdSlotInterface object');

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