<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

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

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getDisplayBlacklistsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PagerParam $param
     * @return mixed
     */
    public function getDisplayBlacklistsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param);

    /**
     * get BlackList for AdSlot
     *
     * @param BaseAdSlotInterface $adSlot
     * @return array
     */
    public function getBlacklistForAdSlot(BaseAdSlotInterface $adSlot);

    /**
     * get BlackList for AdSlot
     *
     * @param BaseLibraryAdSlotInterface $libAdSlot
     * @return array
     */
    public function getBlacklistForLibAdSlot(BaseLibraryAdSlotInterface $libAdSlot);

}