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
     */
    public function __construct(ReportTypeInterface $reportType, array $reports, $name, DateTime $startDate, DateTime $endDate,
        $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue
    )
    {
        parent::__construct($reportType, $reports, $name, $startDate, $endDate,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->billedAmount = $billedAmount;
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


}