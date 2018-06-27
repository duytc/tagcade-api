<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class PlatformStatistics
{
    protected $reportGroup;

    protected $reports;

    function __construct(ReportGroup $reportGroup = null)
    {
        $this->reportGroup = $reportGroup;

        $this->reports = $reportGroup != null ? $reportGroup->getReports() : array();
    }

    public function getReportGroup()
    {
        return $this->reportGroup;
    }

    public function getReports()
    {
        return $this->reports;
    }

    public function setReports($reports)
    {
        $this->reports = $reports;

        return $this;
    }
}