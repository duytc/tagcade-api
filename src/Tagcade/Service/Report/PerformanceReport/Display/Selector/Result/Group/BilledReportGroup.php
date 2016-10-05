<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use DateTime;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class BilledReportGroup extends ReportGroup implements BilledReportDataInterface
{
    use CalculateWeightedValueTrait;
    use CalculateRatiosTrait;
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
    protected $hbRequests;
    protected $hbBilledAmount;
    protected $hbBilledRate;
    protected $rtbImpressions;
    protected $averageSlotOpportunities;
    protected $averageHbRequests;
    protected $averageRtbImpressions;
    protected $averageBilledAmount;
    protected $averageHbBilledAmount;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param string $name
     * @param int $totalOpportunities
     * @param int $slotOpportunities
     * @param int $hbRequests
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     * @param float $billedAmount
     * @param float $hbBilledAmount
     * @param float $estCpm
     * @param float $estRevenue
     * @param float $averageTotalOpportunities
     * @param float $averageImpressions
     * @param float $averagePassbacks
     * @param float $averageEstCpm
     * @param float $averageEstRevenue
     * @param float $averageFillRate
     * @param float $averageSlotOpportunities
     * @param float $averageHbRequests
     * @param $averageBilledAmount
     * @param $averageHbBilledAmount
     * @param $rtbImpressions
     * @param $averageRtbImpressions
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name,
        $totalOpportunities, $slotOpportunities, $hbRequests, $impressions, $passbacks, $fillRate, $billedAmount, $hbBilledAmount, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities, $averageHbRequests, $averageBilledAmount,
        $averageHbBilledAmount, $rtbImpressions, $averageRtbImpressions
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->hbRequests = $hbRequests;
        $this->rtbImpressions = $rtbImpressions;
        $this->billedAmount = round($billedAmount, 4);
        $this->hbBilledAmount = round($hbBilledAmount, 4);
        $this->averageSlotOpportunities = round($averageSlotOpportunities);
        $this->averageHbRequests = round($averageHbRequests);
        $this->averageRtbImpressions = round($averageRtbImpressions);
        $this->averageBilledAmount = round($averageBilledAmount, 4);
        $this->averageHbBilledAmount = round($averageHbBilledAmount, 4);

        $this->hbBilledRate = $this->calculateWeightedValue($this->reports, 'hbBilledRate', 'hbBilledAmount');
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

    /**
     * @return float
     */
    public function getAverageHbBilledAmount()
    {
        return $this->averageHbBilledAmount;
    }

    /**
     * @return float
     */
    public function getAverageHbRequests()
    {
        return $this->averageHbRequests;
    }

    /**
     * @return int
     */
    public function getHbRequests()
    {
        return $this->hbRequests;
    }

    /**
     * @return float
     */
    public function getHbBilledAmount()
    {
        return $this->hbBilledAmount;
    }

    /**
     * @return float|null
     */
    public function getHbBilledRate()
    {
        return $this->hbBilledRate;
    }
}