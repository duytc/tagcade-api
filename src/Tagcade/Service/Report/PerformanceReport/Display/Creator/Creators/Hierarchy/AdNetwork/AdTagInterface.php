<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;

interface AdTagInterface extends CreatorInterface
{
    /**
     * @param AdTagReportType $reportType
     * @return AdTagReportInterface
     */
    public function doCreateReport(AdTagReportType $reportType);
}