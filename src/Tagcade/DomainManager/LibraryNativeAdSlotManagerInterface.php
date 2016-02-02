<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryNativeAdSlotManagerInterface extends ManagerInterface {
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryNativeAdSlotInterface[]
     */
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * generate AdSlot From Library For Channels (BY ids!) And Sites (BY ids!)
     *
     * @param LibraryNativeAdSlotInterface $slotLibrary
     * @param array|ChannelInterface[] $channels
     * @param array SiteInterface[] $sites
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(LibraryNativeAdSlotInterface $slotLibrary, $channels, $sites);
}