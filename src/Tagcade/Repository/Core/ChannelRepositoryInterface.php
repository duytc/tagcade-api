<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface ChannelRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getChannelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getChannelsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Channels Include Sites Unreferenced To Library AdSlot
     *
     * @param BaseLibraryAdSlotInterface $slotLibrary
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getChannelsIncludeSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null);

    /**
     * get Channels Include at least one Site for user (Admin or Publisher)
     *
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getChannelsHaveSiteForUser(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getChannelsForUserWithPagination(UserRoleInterface $user, PagerParam $param);
}