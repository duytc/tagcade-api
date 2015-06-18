<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Behavior\ArrayTrait;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdSlotAbstractInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;

class AdSlotManager implements AdSlotManagerInterface
{
    use ArrayTrait;
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

    public function __construct(DisplayAdSlotManagerInterface $displayAdSlotManager,
        NativeAdSlotManagerInterface $nativeAdSlotManager,
        DynamicAdSlotManagerInterface $dynamicAdSlotManager
    )
    {
        $this->displayAdSlotManager = $displayAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
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
    public function save(AdSlotAbstractInterface $adSlot)
    {
        $this->getManager($adSlot)->save($adSlot);
    }

    /**
     * @inheritdoc
     */
    public function delete(AdSlotAbstractInterface $adSlot)
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
        $found = $this->displayAdSlotManager->find($id);
        if (null !== $found) {
            return $found;
        }

        $found = $this->nativeAdSlotManager->find($id);
        if (null !== $found) {
            return $found;
        }

        $found = $this->dynamicAdSlotManager->find($id);

        return $found;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        $allDisplayAdSlots = $this->displayAdSlotManager->all();
        $allNativeAdSlots = $this->nativeAdSlotManager->all();
        $allDynamicAdSlots = $this->dynamicAdSlotManager->all();

        return $this->sliceArray(array_merge($allDisplayAdSlots, $allNativeAdSlots, $allDynamicAdSlots), $limit, $offset);
    }

    public function allReportableAdSlots($limit = null, $offset = null)
    {
        $allDisplayAdSlots = $this->displayAdSlotManager->all();
        $allNativeAdSlots = $this->nativeAdSlotManager->all();

        return $this->sliceArray(array_merge($allDisplayAdSlots, $allNativeAdSlots), $limit, $offset);
    }


    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $displayAdSlots = $this->displayAdSlotManager->getAdSlotsForSite($site);
        $nativeAdSlots = $this->nativeAdSlotManager->getNativeAdSlotsForSite($site);
        $dynamicAdSlots = $this->dynamicAdSlotManager->getDynamicAdSlotsForSite($site);

        return $this->sliceArray(array_merge($displayAdSlots, $nativeAdSlots, $dynamicAdSlots), $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $displayAdSlots = $this->displayAdSlotManager->getAdSlotsForPublisher($publisher);
        $nativeAdSlots = $this->nativeAdSlotManager->getNativeAdSlotsForPublisher($publisher);
        $dynamicAdSlots = $this->dynamicAdSlotManager->getDynamicAdSlotsForPublisher($publisher);

        return $this->sliceArray(array_merge($displayAdSlots, $nativeAdSlots, $dynamicAdSlots), $limit, $offset);
    }


    /**
     * @param AdSlotAbstractInterface $adSlot
     * @return DisplayAdSlotManagerInterface|NativeAdSlotManagerInterface|DynamicAdSlotManagerInterface
     */
    protected function getManager(AdSlotAbstractInterface $adSlot)
    {
        if ($adSlot instanceof AdSlotInterface) {
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