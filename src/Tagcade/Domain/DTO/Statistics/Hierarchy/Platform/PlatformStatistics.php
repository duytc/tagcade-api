<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class PlatformStatistics
{
    protected $reportGroup;

    protected $reports;

    function __construct(ReportGroup $reportGroup)
    {
        $this->reportGroup = $reportGroup;

        $historicalReports = $reportGroup->getReports();
        array_shift($historicalReports); // Ignore today statistics
        $this->reports = $historicalReports;
    }

    public function getReportGroup()
    {
        return $this->reportGroup;
    }

    public function getReports()
    {
        return $this->reports;
    }
}