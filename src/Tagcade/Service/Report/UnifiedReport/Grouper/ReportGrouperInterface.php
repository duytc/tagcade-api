<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\UnifiedReport\Result\UnifiedReportResultInterface;

interface ReportGrouperInterface
{
    /**
     * @param UnifiedReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(UnifiedReportResultInterface $reportCollection);
}
