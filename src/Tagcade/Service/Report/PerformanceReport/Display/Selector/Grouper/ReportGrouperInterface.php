<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

interface ReportGrouperInterface
{
    /**
     * @param ReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportResultInterface $reportCollection);
}
