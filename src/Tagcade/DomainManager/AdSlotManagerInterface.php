<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotManagerInterface extends ManagerInterface
{
    public function allReportableAdSlots($limit = null, $offset = null);

    public function allReportableAdSlotIds();

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

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAdSlotsForRonAdSlot(RonAdSlotInterface $ronAdSlot, $limit = null, $offset = null);

    public function getReportableAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getReportableAdSlotIdsRelatedAdNetwork(AdNetworkInterface $adNetwork);
}