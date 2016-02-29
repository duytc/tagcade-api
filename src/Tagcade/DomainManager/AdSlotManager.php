<?php

namespace Tagcade\DomainManager;

use InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
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

    public function __construct(AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
    }

    public function setDisplayAdSlotManager(DisplayAdSlotManagerInterface $displayAdSlotManager) {
        $this->displayAdSlotManager = $displayAdSlotManager;
    }

    public function setNativeAdSlotManager(NativeAdSlotManagerInterface $nativeAdSlotManager) {
        $this->nativeAdSlotManager = $nativeAdSlotManager;
    }

    public function setDynamicAdSlotManager(DynamicAdSlotManagerInterface $dynamicAdSlotManager) {
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
    public function save(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof BaseAdSlotInterface) throw new InvalidArgumentException('expect BaseAdSlotInterface object');

        $this->getManager($adSlot)->save($adSlot);
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof BaseAdSlotInterface) throw new InvalidArgumentException('expect BaseAdSlotInterface object');

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

    public function allReportableAdSlotIds()
    {
        return $this->adSlotRepository->allReportableAdSlotIds();
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

    public function getReportableAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getReportableAdSlotIdsForSite($site, $limit, $offset);
    }

    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getReportableAdSlotIdsForPublisher($publisher, $limit, $offset);
    }


    /**
     * @param BaseAdSlotInterface|BaseLibraryAdSlotInterface $adSlot
     * @return DisplayAdSlotManagerInterface|NativeAdSlotManagerInterface|DynamicAdSlotManagerInterface
     */
    protected function getManager($adSlot)
    {
        if ($adSlot instanceof DisplayAdSlotInterface || $adSlot instanceof LibraryDisplayAdSlotInterface) {
            return $this->displayAdSlotManager;
        }

        if ($adSlot instanceof NativeAdSlotInterface || $adSlot instanceof LibraryNativeAdSlotInterface) {
            return $this->nativeAdSlotManager;
        }

        if ($adSlot instanceof DynamicAdSlotInterface || $adSlot instanceof LibraryDynamicAdSlotInterface) {
            return $this->dynamicAdSlotManager;
        }

        throw new LogicException('Do not support manager for this type of ad slot');


    }

    public function persistAndFlush(BaseAdSlotInterface $adSlot)
    {
        $this->getManager($adSlot)->persistAndFlush($adSlot);
    }

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return BaseAdSlotInterface[]
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site)
    {
        return $this->getManager($libraryAdSlot)->getReferencedAdSlotsForSite($libraryAdSlot, $site);
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAdSlotsForRonAdSlot(RonAdSlotInterface $ronAdSlot, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getByRonAdSlot($ronAdSlot, $limit, $offset);
    }

    public function getReportableAdSlotIdsRelatedAdNetwork(AdNetworkInterface $adNetwork)
    {
        return $this->adSlotRepository->getReportableAdSlotIdsRelatedAdNetwork($adNetwork);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsRelatedChannelForUser(UserRoleInterface $user, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getAdSlotsRelatedChannelForUser($user, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForChannel(ChannelInterface $channel, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getAdSlotsForChannel($channel, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }
}