<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\NativeAdSlotRepositoryInterface;

class NativeAdSlotManager implements NativeAdSlotManagerInterface
{
    protected $om;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;

    public function __construct(ObjectManager $om, NativeAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, NativeAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(NativeAdSlotInterface $nativeAdSlot)
    {
        $this->om->persist($nativeAdSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(NativeAdSlotInterface $nativeAdSlot)
    {
        $this->om->remove($nativeAdSlot);
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
    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForPublisher($publisher, $limit, $offset);
    }
}