<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class BilledReportGroup extends ReportGroup implements BilledReportDataInterface
{
    // inherited properties
    protected $reportType;
    protected $reports;
    protected $name;
    protected $startDate;
    protected $endDate;
    protected $fillRate;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $estCpm;
    protected $estRevenue;

    protected $averageTotalOpportunities;
    protected $averageImpressions;
    protected $averagePassbacks;
    protected $averageEstCpm;
    protected $averageEstRevenue;
    protected $averageFillRate;

    // new properties
    protected $slotOpportunities;
    protected $billedAmount;
    protected $rtbImpressions;
    protected $averageSlotOpportunities;
    protected $averageRtbImpressions;
    protected $averageBilledAmount;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param string $name
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
     * @param $averageBilledAmount
     * @param $rtbImpressions
     * @param $averageRtbImpressions
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name,
        $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities, $averageBilledAmount,
        $rtbImpressions, $averageRtbImpressions
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->rtbImpressions = $rtbImpressions;
        $this->billedAmount = round($billedAmount, 4);
        $this->averageSlotOpportunities = round($averageSlotOpportunities);
        $this->averageRtbImpressions = round($averageRtbImpressions);
        $this->averageBilledAmount = round($averageBilledAmount, 4);
    }

    /**
     * @return int
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    public function getRtbImpressions()
    {
        return $this->rtbImpressions;
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

    public function getAverageRtbImpressions()
    {
        return $this->averageRtbImpressions;
    }

    /**
     * @return float
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }


}