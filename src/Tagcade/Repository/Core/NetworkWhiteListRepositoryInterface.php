<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface NetworkWhiteListRepositoryInterface extends ObjectRepository
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function all($limit = null, $offset = null);

    /**
     * @param DisplayWhiteListInterface $displayWhiteList
     * @return mixed
     */
    public function getForDisplayWhiteList(DisplayWhiteListInterface $displayWhiteList);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getForAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @param PublisherInterface $publisher
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function getNetworkWhiteListForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}