<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param AdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param AdSlotInterface $adSlot
     * @return void
     */
    public function save(AdSlotInterface $adSlot);

    /**
     * @param AdSlotInterface $adSlot
     * @return void
     */
    public function delete(AdSlotInterface $adSlot);

    /**
     * @return AdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return AdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return AdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return AdSlotInterface[]
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdSlotInterface[]
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}