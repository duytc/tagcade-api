<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdTagRepositoryInterface extends ObjectRepository
{
    /**
     * @param ReportableAdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null);

    public function getAdTagIdsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null);

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getSharedAdTagsForAdSlot(BaseAdSlotInterface $adSlot, $limit = null, $offset = null);

    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForSite(SiteInterface $site, $limit = null, $offset = null);

    public function getAdTagsForSiteWithPagination(SiteInterface $site, PagerParam $param);

    public function getAdTagIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getActiveAdTagsIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getActiveAdTagIdsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    public function getAllActiveAdTagIds();

    public function getAdTagsForAdNetworkQuery(AdNetworkInterface $adNetwork);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagIdsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param);

    public function getAdTagsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param);

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    /**
     * get AdTags For AdNetwork And Site With SubPublisher
     *
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param SubPublisherInterface $subPublisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getAdTagsForAdNetworkAndSiteWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    public function getAdTagsByAdSlotAndRefId(BaseAdSlotInterface $adSlot, $refId, $limit = null, $offset = null);

    public function getAdTagsByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null);

    /**
     * get all AdTags By LibraryAdSlot And Differ RefId (not include the ad tag with refId)
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getAdTagsByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null);

    /**
     * @param AdNetworkPartnerInterface $partner
     * @param UserRoleInterface $user
     * @param null $partnerTagId
     * @return array
     */
    public function getAdTagsForPartner(AdNetworkPartnerInterface $partner, UserRoleInterface $user, $partnerTagId = null);

    /**
     * @param $status
     * @return mixed
     */
    public function getAllAdTagsByStatus($status);

    /**
     * @param $status
     * @return mixed
     */
    public function getAdTagsThatSetImpressionAndOpportunityCapByStatus ($status);

    /**
     * @param LibraryAdTag $libraryAdTag
     * @return mixed
     */
    public function getAdTagsHaveTheSameAdTabLib(LibraryAdTag $libraryAdTag);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @return mixed
     */
    public function isSiteActiveForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface|null $publisher
     * @return mixed
     */
    public function getActiveSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param $status
     * @return mixed
     */
    public function countAdTagForSiteAndAdNetworkByStatus(AdNetworkInterface $adNetwork, SiteInterface $site, $status);
}