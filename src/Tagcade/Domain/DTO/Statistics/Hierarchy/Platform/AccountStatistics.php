<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class AccountStatistics
{
    protected $reportGroup;

    protected $reports;

    function __construct(ReportGroup $reportGroup = null)
    {
        $this->reportGroup = $reportGroup;

        $this->reports = $reportGroup != null ? $reportGroup->getReports() : array();
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