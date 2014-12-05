<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

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