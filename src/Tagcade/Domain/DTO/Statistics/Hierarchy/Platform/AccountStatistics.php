<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class AccountStatistics
{
    protected $reportGroup;

    protected $reports;

    function __construct(ReportGroup $reportGroup, $includedToday = false)
    {
        $this->reportGroup = $reportGroup;

        $historicalReports = $reportGroup->getReports();
        if ($includedToday) {
            array_shift($historicalReports); // Ignore today statistics
        }

        $this->reports = $historicalReports;
    }

    /**
     * @return ReportGroup
     */
    public function getReportGroup()
    {
        return $this->reportGroup;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }


}