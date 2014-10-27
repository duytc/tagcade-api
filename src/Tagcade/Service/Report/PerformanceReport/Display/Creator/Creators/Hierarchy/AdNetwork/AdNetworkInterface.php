<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;

interface AdNetworkInterface extends CreatorInterface
{
    /**
     * @param AdNetworkReportType $reportType
     * @return AdNetworkReportInterface
     */
    public function doCreateReport(AdNetworkReportType $reportType);
}