<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class PlatformStatistics
{
    protected $reportGroup;

    protected $reports;

    function __construct(ReportGroup $reportGroup = null, $includedToday = false)
    {
        $this->reportGroup = $reportGroup;

        $historicalReports = $reportGroup != null ? $reportGroup->getReports() : array();
        if (true === $includedToday) {
            array_shift($historicalReports); // Ignore today statistics
        }

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