<?php

namespace Tagcade\Domain\DTO\Statistics;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class DaySummary
{
    /**
     * @var ReportInterface
     */
    protected $report;

    function __construct(ReportInterface $report = null)
    {
        $this->report = $report;
    }

    /**
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }


}