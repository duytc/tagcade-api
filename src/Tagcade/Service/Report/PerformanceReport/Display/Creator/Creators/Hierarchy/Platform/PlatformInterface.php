<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;

interface PlatformInterface extends CreatorInterface
{
    /**
     * @param PlatformReportType $reportType
     * @return PlatformReportInterface
     */
    public function doCreateReport(PlatformReportType $reportType);
}