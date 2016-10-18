<?php

namespace Tagcade\Service\Report\SourceReport\Selector;

use DateTime;
use Tagcade\Model\Core\SiteInterface;

interface ReportSelectorInterface {

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    /**
     * Get reports for multiple report types over a date range
     *
     * i.e a report for multiple or all ad networks over a date range
     *
     * @param array $sites
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getMultipleSiteReports(array $sites, DateTime $startDate, DateTime $endDate);

    /**
     * @param array $publishers
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getMultiplePublisherReport(array $publishers, DateTime $startDate, DateTime $endDate);
} 