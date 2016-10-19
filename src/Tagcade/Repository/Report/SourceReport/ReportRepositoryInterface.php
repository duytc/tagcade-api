<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\SourceReport\Report as ReportModel;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportRepositoryInterface
{
    /**
     * Retrieved source reports between a date range
     *
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportModel[]
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getTotalVideoImpressionForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getTotalVideoVisitForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getTotalVideoImpressionForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getTotalVideoVisitForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $dateTime
     * @return mixed
     */
    public function getSourceReportsForPublisher(PublisherInterface $publisher, DateTime $dateTime);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getBillingReportForPublisherByDay(PublisherInterface $publisher, DateTime $startDate , DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getBillingReportForPublisherBySite(PublisherInterface $publisher, DateTime $startDate , DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getBillingReportForPlatformByPublisher(DateTime $startDate , DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getBillingReportForPlatformByDay(DateTime $startDate , DateTime $endDate);
}