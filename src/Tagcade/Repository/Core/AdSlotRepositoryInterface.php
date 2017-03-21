<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdSlotRepositoryInterface extends ObjectRepository
{
    public function allReportableAdSlotIds();

    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param PagerParam $param
     * @return mixed
     */
    public function getAdSlotsForSiteWithPagination(SiteInterface $site, $param);

    public function getAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getDisplayAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @inheritdoc
     */
    public function getReportableAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function allReportableAdSlots($limit = null, $offset = null);

    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site, $limit = null, $offset = null);

    public function getCoReferencedAdSlots(BaseLibraryAdSlotInterface $libraryAdSlot);

    /**
     * @param PublisherInterface $publisher
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $domain
     * @return null|BaseAdSlotInterface
     */
    public function getAdSlotForPublisherAndDomainAndLibraryAdSlot(PublisherInterface $publisher, BaseLibraryAdSlotInterface $libraryAdSlot, $domain);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getByRonAdSlot(RonAdSlotInterface $ronAdSlot, $limit = null, $offset = null);

    public function getAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getReportableAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getReportableAdSlotForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getReportableAdSlotIdsRelatedAdNetwork(AdNetworkInterface $adNetwork);

    public function getReportableAdSlotRelatedAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * getAdSlotsRelatedChannelForUser
     *
     * @param UserRoleInterface $user
     * @param null|int $limit [option]
     * @param null|int $offset [option]
     * @return mixed
     */
    public function getAdSlotsRelatedChannelForUser(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * get AdSlots For Channel
     *
     * @param ChannelInterface $channel
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getAdSlotsForChannel(ChannelInterface $channel, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @internal param PublisherInterface $publisher
     * @internal param null $limit
     * @internal param null $offset
     * @return mixed
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getRelatedChannelWithPagination(UserRoleInterface $user, PagerParam $param);

    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @param PagerParam $param
     * @return mixed
     */
    public function getReportableAdSlotQuery(PublisherInterface $publisher, PagerParam $param, $limit = null, $offset = null );

    /**
     * @param PublisherInterface $publisher
     * @param $siteName
     * @param $adSlotName
     * @return mixed
     */
    public function getAdSlotBySiteNameAndAdSlotNameForPublisher( PublisherInterface $publisher, $siteName, $adSlotName);

    /**
     * @param PublisherInterface $publisher
     * @param $adSlotName
     * @return mixed
     */
    public function getAdSlotByNameForPublisher( PublisherInterface $publisher, $adSlotName);

    /**
     * @param $libraryDisplayAdSlot
     * @return mixed
     */
    public function getDisplayAdSlostByLibrary ($libraryDisplayAdSlot);
}