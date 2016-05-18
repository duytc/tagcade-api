<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface LibraryAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * get Library AdSlots For Publisher Query
     *
     * IMPORTANT: only get all ad slots that are used for SHARING (visible = true), support limit, offset
     * do not confuse and use wrong
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Library Display AdSlots For Publisher Query, same getLibraryAdSlotsForPublisher()
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Library Native AdSlots For Publisher Query, same getLibraryAdSlotsForPublisher()
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Library Dynamic AdSlots For Publisher Query, same getLibraryAdSlotsForPublisher()
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAllLibraryAdSlotsUnusedInRon($publisherId = null, $limit = null, $offset = null);

    public function getAllLibraryAdSlotsUsedInRon($publisherId = null, $limit = null, $offset = null);

    public function getAllActiveLibraryAdSlots($limit = null, $offset = null);

    /**
     * get All Library AdSlots For Publisher Query
     *
     * VERY IMPORTANT: get all library ad slots WITHOUT checking visible = true/false and order!!!
     *
     * @param PublisherInterface $publisher
     * @return \Doctrine\ORM\QueryBuilder|mixed
     */
    public function getAllLibraryAdSlotsForPublisherQuery(PublisherInterface $publisher);

    /**
     * get Library AdSlots With Pagination
     *
     * VERY IMPORTANT: get all library ad slots WITH visible = true and without order, limit, offset!!!
     *
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return \Doctrine\ORM\QueryBuilder|mixed
     */
    public function getLibraryAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param);
}