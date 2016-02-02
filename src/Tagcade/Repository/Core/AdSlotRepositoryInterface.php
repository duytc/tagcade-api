<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdSlotRepositoryInterface extends ObjectRepository
{
    public function allReportableAdSlotIds();
    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

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

    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

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
}