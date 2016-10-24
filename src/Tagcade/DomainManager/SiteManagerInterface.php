<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SiteManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return SiteInterface[]
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param array $publishers
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesForPublishers(array $publishers, $limit = null, $offset = null);

    public function getRTBEnabledSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Sites That Have Ad Tags Belonging To Partner(s) for a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get Sites That Have Ad Tags Belonging To a Partner
     *
     * @param AdNetworkInterface $adNetwork
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartner(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * get Sites That Have Ad Tags Belonging To a Partner with a SubPublisher
     *
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, $limit = null, $offset = null);

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getSitesThatHaveSourceReportConfigForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true);

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher);

    /**
     * get all sites that enable sourceReportConfig
     *
     * @param bool $enableSourceReport
     *
     * @return SiteInterface[]
     */
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
     * Delete one channel for a site (in list channels of site)
     *
     * @param SiteInterface $site
     * @param $channelId
     * @return int number of removed channels
     */
    public function deleteChannelForSite(SiteInterface $site, $channelId) ;

    /**
     * @param $domain
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getSiteByDomainAndPublisher($domain, PublisherInterface $publisher);

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getUniqueDomainsForPublisher(PublisherInterface $publisher);

    /**
     * @param $siteToken
     * @return mixed
     */
    public function getSiteBySiteToken($siteToken);

    /**
     * @param PublisherInterface $publisher
     * @param $siteName
     * @return mixed
     */
    public function getSiteByPublisherAndSiteName(PublisherInterface $publisher, $siteName);

    /**
     * @param ModelInterface $sites
     * @return mixed
     */
    public function persists(ModelInterface $sites);

    public function flush();


}