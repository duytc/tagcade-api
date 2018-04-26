<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\SiteInterface;

interface SiteRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param array $publishers
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesForPublishers(array $publishers, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAutoCreatedSites(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getManualCreatedSites(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getSitesForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Sites That Have AdTags Belonging To Partner(s) For Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Sites That Have AdTags Belonging To a Partner
     *
     * @param AdNetworkInterface $adNetwork
     * @param null $limit
     * @param null $offset
     * @internal param PublisherInterface $publisher
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartner(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Sites That Have AdTags Belonging To a Partner
     *
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param null $limit
     * @param null $offset
     * @internal param PublisherInterface $publisher
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, $limit = null, $offset = null);

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * * Get all sites that have configured or not configured for source report by publisher
     *
     * @param PublisherInterface $publisher
     * @param bool $hasSourceReportConfig
     * @return array
     */
    public function getSitesThatHastConfigSourceReportForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true);

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true);

    public function getAllSitesThatEnableSourceReport($enableSourceReport = true);

    /**
     * get all Sites which have no Ad Slot references to a library Ad Slot
     *
     * @param $slotLibrary
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null);

    /**
     * Check if a site with the given domain already existed
     *
     * @param PublisherInterface $publisher
     * @param $domain
     * @param bool $useHash using hash to find site by domain and publisher. The hash is md5 publisher id and its domain
     * @return null|SiteInterface[]
     */
    public function getSitesByDomainAndPublisher(PublisherInterface $publisher, $domain, $useHash = false);

    /**
     * get Sites Not Belong To SubPublisher for a Publisher
     *
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return null|SiteInterface[]
     */
    public function getSitesNotBelongToSubPublisherForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getUniqueDomainsForPublisher(PublisherInterface $publisher);

    /**
     * @param $domain
     * @return mixed
     */
    public function getSitesByDomain($domain);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesForUserQuery(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @param $autoCreate = null
     * @param $enableSourceReport = null
     * @param null $autoOptimize
     * @return QueryBuilder
     */
    public function getSitesForUserWithPagination(UserRoleInterface $user, PagerParam $param, $autoCreate = null, $enableSourceReport = null, $autoOptimize = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface|null $publisher
     * @return mixed
     */
    public function getSiteHavingAdTagBelongsToAdNetworkFilterByPublisher(AdNetworkInterface $adNetwork, $publisher = null);

    public function findSubPublisherByDomainFilterPublisher(PublisherInterface $publisher, $domain);

    /**
     * @param $siteToken
     * @return mixed
     */
    public function getSiteBySiteToken($siteToken);

    /**
     * @param PublisherInterface $publisher
     * @param $domainName
     * @return mixed
     */
    public function getSiteByPublisherAndSiteName(PublisherInterface $publisher, $domainName);

}