<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;

interface SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate);

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getSiteReportForAllPartners(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    public function overrideReport(AdNetworkReportInterface $superReport, SiteReportInterface $report);
}