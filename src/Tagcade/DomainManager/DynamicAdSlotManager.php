<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;

class DynamicAdSlotManager implements DynamicAdSlotManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, DynamicAdSlotRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, DynamicAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(DynamicAdSlotInterface $adSlot)
    {
        $this->om->persist($adSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(DynamicAdSlotInterface $adSlot)
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
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getDynamicAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getDynamicAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForAdSlot(AdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getDynamicAdSlotsForAdSlot($adSlot, $limit, $offset);
    }
}