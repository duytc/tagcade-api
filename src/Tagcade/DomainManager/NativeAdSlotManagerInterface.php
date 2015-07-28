<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface NativeAdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param NativeAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @return void
     */
    public function save(NativeAdSlotInterface $nativeAdSlot);

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @return void
     */
    public function delete(NativeAdSlotInterface $nativeAdSlot);

    /**
     * @return NativeAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return NativeAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return NativeAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return NativeAdSlotInterface[]
     */
    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return NativeAdSlotInterface[]
     */
    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function persistAndFlush(NativeAdSlotInterface $adSlot);
}