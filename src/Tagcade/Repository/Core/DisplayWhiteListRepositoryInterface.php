<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface DisplayWhiteListRepositoryInterface extends ObjectRepository
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
    public function getDisplayWhiteListsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getWhiteListsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getDisplayWhiteListsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PagerParam $param
     * @return mixed
     */
    public function getDisplayWhiteListsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param);
}