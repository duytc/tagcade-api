<?php

namespace Tagcade\Domain\DTO\Statistics;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

class ProjectedBilling
{
    /**
     * @var ReportGroup
     */
    private $reportGroup;
    /**
     * @var float
     */
    private $projectedBilledAmount;

    function __construct(ReportGroup $reportGroup, $projectedBilledAmount)
    {
        $this->reportGroup = $reportGroup;
        $this->projectedBilledAmount = round($projectedBilledAmount, 4);
    }

    /**
     * @return ReportGroup
     */
    public function getReportGroup()
    {
        return $this->reportGroup;
    }

    /**
     * @return float
     */
    public function getProjectedBilledAmount()
    {
        return $this->projectedBilledAmount;
    }
}