<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class CalculatedReportGroup extends ReportGroup
{
    private $slotOpportunities;

    /**
     * @param ReportTypeInterface $reportType
     * @param string $name
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $totalOpportunities
     * @param int $slotOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     */
    public function __construct(ReportTypeInterface $reportType, $name, DateTime $startDate, DateTime $endDate, $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate)
    {
        parent::__construct($reportType, $name, $startDate, $endDate, $totalOpportunities, $impressions, $passbacks, $fillRate);

        $this->slotOpportunities = $slotOpportunities;
    }

    /**
     * @return int
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }
}