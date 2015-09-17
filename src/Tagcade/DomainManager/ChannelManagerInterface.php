<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ChannelManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return ChannelInterface[]
     */
    public function getChannelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * Delete one site for a channel (in list sites of channel)
     *
     * @param ChannelInterface $channel
     * @param $siteId
     * @return int number of removed sites
     */
    public function deleteSiteForChannel(ChannelInterface $channel, $siteId) ;

    /**
     * get Channels Include Sites Unreferenced To Library AdSlot
     *
     * @param BaseLibraryAdSlotInterface $slotLibrary
     * @param null $limit
     * @param null $offset
     * @return ChannelInterface[]
     */
    public function getChannelsIncludeSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null) ;
}