<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface NetworkBlacklistManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDisplayBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getByAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDefaultBlacklists(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param DisplayBlacklistInterface $displayBlacklist
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function setDefaultBlacklists(PublisherInterface $publisher, DisplayBlacklistInterface $displayBlacklist, $limit = null, $offset = null);
}