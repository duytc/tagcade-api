<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;

interface SiteReportRepositoryInterface
{
    /**
     * @param SiteInterface $site
     * @param AdNetworkInterface $adNetwork
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportFor(SiteInterface $site, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate);

    public function overrideReport(AdNetworkReportInterface $superReport, SiteReportInterface $report);
}