<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDisplayAdSlotManagerInterface extends ManagerInterface {
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * generate AdSlot From Library For Channels (BY ids!) And Sites (BY ids!)
     *
     * @param LibraryDisplayAdSlotInterface $slotLibrary
     * @param array|ChannelInterface[] $channels
     * @param array SiteInterface[] $sites
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(LibraryDisplayAdSlotInterface $slotLibrary, $channels, $sites);
}