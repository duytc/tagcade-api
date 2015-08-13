<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface NativeAdSlotManagerInterface extends ManagerInterface
{
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

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site);
}