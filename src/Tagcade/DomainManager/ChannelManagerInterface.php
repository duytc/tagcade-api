<?php

namespace Tagcade\DomainManager;

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
}