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
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getSourceReportsForPublisher(PublisherInterface $publisher);
}