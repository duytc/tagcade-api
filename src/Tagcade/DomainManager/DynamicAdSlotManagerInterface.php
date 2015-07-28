<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DynamicAdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param DynamicAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @return void
     */
    public function save(DynamicAdSlotInterface $adSlot);

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @return void
     */
    public function delete(DynamicAdSlotInterface $adSlot);

    /**
     * @return DynamicAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return DynamicAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return DynamicAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return DynamicAdSlotInterface[]
     */
    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return DynamicAdSlotInterface[]
     */
    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

//    /**
//     * @param DisplayAdSlotInterface $adSlot
//     * @param int|null $limit
//     * @param int|null $offset
//     * @return DynamicAdSlotInterface[]
//     */
//    public function getDynamicAdSlotsForAdSlot(DisplayAdSlotInterface $adSlot, $limit = null, $offset = null);

    public function persistAndFlush(DynamicAdSlotInterface $adSlot);
}