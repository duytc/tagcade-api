<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayAdSlotManagerInterface extends ManagerInterface{
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

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site);

    /**
     * @param SiteInterface $site
     * @param $name
     * @return mixed
     */
    public function getAdSlotForSiteByName(SiteInterface $site, $name);
}