<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class CalculatedReportGroup extends ReportGroup
{
    private $slotOpportunities;
    private $billedAmount;

    private $averageSlotOpportunities;
    /**
     * @param ReportTypeInterface $reportType
     * @param ReportInterface[] $reports
     * @param string $name
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $totalOpportunities
     * @param int $slotOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     * @param float $billedAmount
     * @param float $estCpm
     * @param float $estRevenue
     * @param float $averageTotalOpportunities
     * @param float $averageImpressions
     * @param float $averagePassbacks
     * @param float $averageEstCpm
     * @param float $averageEstRevenue
     * @param float $averageFillRate
     * @param float $averageSlotOpportunities
     */
    public function __construct(ReportTypeInterface $reportType, array $reports, $name, DateTime $startDate, DateTime $endDate,
        $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities
    )
    {
        parent::__construct($reportType, $reports, $name, $startDate, $endDate,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->billedAmount = round($billedAmount, 4);
        $this->averageSlotOpportunities = round($averageSlotOpportunities);
    }

    /**
     * @return int
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return float
     */
    public function getAverageSlotOpportunities()
    {
        return $this->averageSlotOpportunities;
    }
}