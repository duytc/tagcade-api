<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;

class PlatformStatistics
{
    protected $report;

    function __construct(ReportGroup $report)
    {
        $this->report = $report;
    }

    public function getReport()
    {
        return $this->report;
    }
}