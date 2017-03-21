<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayBlacklistRepositoryInterface extends ObjectRepository
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDisplayBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getBlacklistsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

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
     * @param null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findDisplayBlacklistsByNameForPublisher(PublisherInterface $publisher, $name, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param NetworkBlacklistInterface $networkBlacklist
     * @return mixed
     */
    public function getByNetworkBlacklist(NetworkBlacklistInterface $networkBlacklist);
}