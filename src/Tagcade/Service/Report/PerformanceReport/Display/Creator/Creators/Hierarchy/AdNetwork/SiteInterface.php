<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as AdNetworkSiteReportType;

interface SiteInterface extends CreatorInterface
{
    /**
     * @param AdNetworkSiteReportType $reportType
     * @return SiteReportInterface
     */
    public function doCreateReport(AdNetworkSiteReportType $reportType);
}