<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdSlotManagerInterface extends ManagerInterface
{
    public function allReportableAdSlots($limit = null, $offset = null);

    public function allReportableAdSlotIds();

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseAdSlotInterface[]
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseAdSlotInterface[]
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getReportableAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function persistAndFlush(BaseAdSlotInterface $adSlot);

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAdSlotsForRonAdSlot(RonAdSlotInterface $ronAdSlot, $limit = null, $offset = null);

    public function getReportableAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param array $publishers
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getReportableAdSlotIdsForPublishers(array $publishers, $limit = null, $offset = null);

    public function getReportableAdSlotIdsRelatedAdNetwork(AdNetworkInterface $adNetwork);

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
     * get AdSlots For a Channel
     *
     * @param ChannelInterface $channel
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getAdSlotsForChannel(ChannelInterface $channel, $limit = null, $offset = null);

    /**
     * get Display AdSlots For Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Display AdSlots For Site
     *
     * @param SiteInterface $site
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getDisplayAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param $siteName
     * @param $adSlotName
     * @return mixed
     */
    public function getAdSlotBySiteNameAndAdSlotNameForPublisher( PublisherInterface $publisher, $siteName, $adSlotName);


    /**
     * @param PublisherInterface $publisherInterface
     * @param $adSlotName
     * @return mixed
     */
    public function getAdSlotByNameForPublisher(PublisherInterface $publisherInterface, $adSlotName);

    /**
     * @param $libraryDisplayAdSlot
     * @return mixed
     */
    public function getDisplayAdSlotByLibrary($libraryDisplayAdSlot);
}