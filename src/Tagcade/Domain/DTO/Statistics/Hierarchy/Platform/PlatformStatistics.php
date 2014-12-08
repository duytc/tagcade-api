<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

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