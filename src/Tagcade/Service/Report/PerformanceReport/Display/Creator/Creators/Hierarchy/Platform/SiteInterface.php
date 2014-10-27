<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;

interface SiteInterface extends CreatorInterface
{
    /**
     * @param SiteReportType $reportType
     * @return SiteReportInterface
     */
    public function doCreateReport(SiteReportType $reportType);
}