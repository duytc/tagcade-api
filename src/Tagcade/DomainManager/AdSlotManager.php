<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;

class AdSlotManager implements AdSlotManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, AdSlotRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdSlotInterface $adSlot)
    {
        $this->om->persist($adSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(AdSlotInterface $adSlot)
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
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdSlotsForSite($site, $limit, $offset);
    }
}