<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdTagManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param AdTagInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param AdTagInterface $adTag
     * @return void
     */
    public function save(AdTagInterface &$adTag);

    /**
     * @param AdTagInterface $adTag
     * @return void
     */
    public function delete(AdTagInterface $adTag);

    /**
     * @return AdTagInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return AdTagInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function all($limit = null, $offset = null);

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
     * @param bool $filterActive
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForSite(SiteInterface $site, $filterActive = false, $limit = null, $offset = null);

    public function getAdTagIdsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForPublisher(Publisherinterface $publisher, $limit = null, $offset = null);

    public function getActiveAdTagsIdsForPublisher(Publisherinterface $publisher, $limit = null, $offset = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Ad Tags That Have Partner For AdNetwork
     *
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsThatHavePartnerForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Ad Tags That Have Partner For AdNetwork with a SubPublisher
     *
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsThatHavePartnerForAdNetworkWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, $limit = null, $offset = null);

    public function getAdTagIdsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

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

    public function getAdTagIdsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    public function getAllActiveAdTagIds();

    /**
     * @param AdNetworkInterface $adNetwork
     * @param array $sites
     * @param null $limit
     * @param null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    public function updateAdTagStatusForAdNetwork(AdNetworkInterface $adNetwork, $active = true);

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

    public function updateActiveStateBySingleSiteForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site, $active = false);

    public function getAdTagsThatHavePartner(PublisherInterface $publisher, $uniquePartnerTagId = false, $limit = null, $offset = null);

    public function getAllAdTagsByStatus($status);

}