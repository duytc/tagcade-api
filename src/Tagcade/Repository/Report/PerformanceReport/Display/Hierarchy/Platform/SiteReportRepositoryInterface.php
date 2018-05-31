<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SiteReportRepositoryInterface
{
    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportFor(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumBilledAmountForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**\
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumSlotOpportunities(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**\
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumImpressionOpportunities(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumSlotHbRequests(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * This will return array of pair (site id, billedAmount) sorted by billedAmount desc
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $limit
     * @return array
     */
    public function getTopSitesByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10);

    /**
     * This will return array of pair (site id, estRevenue) sorted by estRevenue desc
     *
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $limit
     * @return mixed
     */
    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $limit = 10);

    /**
     * This will return array of pair (site id, slotOpportunities) sorted by slotOpportunities desc
     *
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $limit
     * @return mixed
     */
    public function getTopSitesForPublisherBySlotOpportunities(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $limit = 10);
    /**
     * @param SiteReportInterface $report
     * @return mixed
     */
    public function overrideReport(SiteReportInterface $report);
}