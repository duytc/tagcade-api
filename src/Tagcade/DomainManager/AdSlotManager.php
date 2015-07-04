<?php

namespace Tagcade\DomainManager;

use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class AdSlotManager implements AdSlotManagerInterface
{
    /**
     * @var DisplayAdSlotManagerInterface
     */
    private $displayAdSlotManager;
    /**
     * @var NativeAdSlotManagerInterface
     */
    private $nativeAdSlotManager;
    /**
     * @var DynamicAdSlotManagerInterface
     */
    private $dynamicAdSlotManager;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;

    public function __construct(DisplayAdSlotManagerInterface $displayAdSlotManager,
        NativeAdSlotManagerInterface $nativeAdSlotManager,
        DynamicAdSlotManagerInterface $dynamicAdSlotManager,
        AdSlotRepositoryInterface $adSlotRepository
    )
    {
        $this->displayAdSlotManager = $displayAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return $this->displayAdSlotManager->supportsEntity($entity) || $this->nativeAdSlotManager->supportsEntity($entity) || $this->dynamicAdSlotManager->supportsEntity($entity);
    }

    /**
     * @inheritdoc
     */
    public function save(BaseAdSlotInterface $adSlot)
    {
        $this->getManager($adSlot)->save($adSlot);
    }

    /**
     * @inheritdoc
     */
    public function delete(BaseAdSlotInterface $adSlot)
    {
        $this->getManager($adSlot)->delete($adSlot);

    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        throw new RuntimeException('Not support create new instance of ad slot via generic AdSlotManager. Use either DisplayAdSlotManager, NativeAdSlotManager or DynamicAdSlotManager');
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->adSlotRepository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->adSlotRepository->findAll();
    }

    public function allReportableAdSlots($limit = null, $offset = null)
    {
       return $this->adSlotRepository->allReportableAdSlots($limit, $offset);
    }


    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getReportableAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
       return $this->adSlotRepository->getReportableAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return DisplayAdSlotManagerInterface|NativeAdSlotManagerInterface|DynamicAdSlotManagerInterface
     */
    protected function getManager(BaseAdSlotInterface $adSlot)
    {
        if ($adSlot instanceof DisplayAdSlotInterface) {
            return $this->displayAdSlotManager;
        }

        if ($adSlot instanceof NativeAdSlotInterface) {
            return $this->nativeAdSlotManager;
        }

        if ($adSlot instanceof DynamicAdSlotInterface) {
            return $this->dynamicAdSlotManager;
        }

        throw new LogicException('Do not support manager for this type of ad slot');


    }
}