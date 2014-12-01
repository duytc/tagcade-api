<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;

class AccountStatistics
{
    protected $report;

    function __construct(ReportGroup $report)
    {
        $this->report = $report;
    }

    /**
     * @return ReportGroup
     */
    public function getReport()
    {
        return $this->report;
    }
}