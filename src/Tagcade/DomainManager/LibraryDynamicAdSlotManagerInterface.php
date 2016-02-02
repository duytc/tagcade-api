<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDynamicAdSlotManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * generate AdSlot From Library For Channels (BY ids!) And Sites (BY ids!)
     *
     * @param LibraryDynamicAdSlotInterface $slotLibrary
     * @param array|ChannelInterface[] $channels
     * @param array SiteInterface[] $sites
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(LibraryDynamicAdSlotInterface $slotLibrary, $channels, $sites);
}