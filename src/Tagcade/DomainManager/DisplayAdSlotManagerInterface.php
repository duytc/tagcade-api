<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayAdSlotManagerInterface {
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param DisplayAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return void
     */
    public function save(DisplayAdSlotInterface $adSlot);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return void
     */
    public function delete(DisplayAdSlotInterface $adSlot);

    /**
     * @return DisplayAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return DisplayAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return DisplayAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return DisplayAdSlotInterface[]
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return DisplayAdSlotInterface[]
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function persistAndFlush(DisplayAdSlotInterface $adSlot);
} 