<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayBlacklistManagerInterface extends ManagerInterface
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
     * @param $name
     * @param $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getDisplayBlacklistsByNameForPublisher(PublisherInterface $publisher, $name, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param NetworkBlacklistInterface $networkBlacklist
     * @return mixed
     */
    public function getByNetworkBlacklist(NetworkBlacklistInterface $networkBlacklist);
}