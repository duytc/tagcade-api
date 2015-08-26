<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;

interface SiteManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return SiteInterface[]
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

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
     * Delete one channel for a site (in list channels of site)
     *
     * @param SiteInterface $site
     * @param $channelId
     * @return int number of removed channels
     */
    public function deleteChannelForSite(SiteInterface $site, $channelId) ;
}