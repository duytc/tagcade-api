<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param BaseAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return void
     */
    public function save(BaseAdSlotInterface $adSlot);

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return void
     */
    public function delete(BaseAdSlotInterface $adSlot);

    /**
     * @return BaseAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return BaseAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    public function allReportableAdSlots($limit = null, $offset = null);


    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseAdSlotInterface[]
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseAdSlotInterface[]
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getReportableAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function persistAndFlush(BaseAdSlotInterface $adSlot);

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site);
}