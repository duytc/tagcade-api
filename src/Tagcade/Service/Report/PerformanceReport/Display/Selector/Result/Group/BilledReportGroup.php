<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use DateTime;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
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
    protected $opportunityFillRate;
    protected $billedAmount;
    protected $averageSlotOpportunities;
    protected $averageOpportunityFillRate;
    protected $averageBilledAmount;

    protected $inBannerRequests;
    protected $inBannerImpressions;
    protected $inBannerTimeouts;
    protected $inBannerBilledRate;
    protected $inBannerBilledAmount;

    protected $averageInBannerRequests;
    protected $averageInBannerImpressions;
    protected $averageInBannerTimeouts;
    protected $averageInBannerBilledRate;
    protected $averageInBannerBilledAmount;

    /**
     * BilledReportGroup constructor.
     * @param ReportTypeInterface|\Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $reports
     * @param string $name
     * @param int $totalOpportunities
     * @param int $slotOpportunities
     * @param int $impressions
     * @param float $passbacks
     * @param float $fillRate
     * @param $billedAmount
     * @param $estCpm
     * @param $estRevenue
     * @param $adOpportunities
     * @param $opportunityFillRate
     * @param $averageTotalOpportunities
     * @param $averageImpressions
     * @param $averagePassbacks
     * @param $averageEstCpm
     * @param $averageEstRevenue
     * @param $averageFillRate
     * @param $averageSlotOpportunities
     * @param $averageBilledAmount
     * @param $inBannerRequests
     * @param $inBannerTimeouts
     * @param $inBannerBilledAmount
     * @param $inBannerImpressions
     * @param $averageInBannerRequests
     * @param $averageInBannerTimeouts
     * @param $averageInBannerBilledAmount
     * @param $averageInBannerImpressions
     * @param $averageAdOpportunities
     * @param $averageOpportunityFillRate
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name,
                                $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue, $adOpportunities, $opportunityFillRate,
                                $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities, $averageBilledAmount,
                                $inBannerRequests, $inBannerTimeouts, $inBannerBilledAmount, $inBannerImpressions, $averageInBannerRequests, $averageInBannerTimeouts, $averageInBannerBilledAmount, $averageInBannerImpressions, $averageAdOpportunities, $averageOpportunityFillRate
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue, $adOpportunities,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageAdOpportunities
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->opportunityFillRate = round($opportunityFillRate, 4);
        $this->billedAmount = round($billedAmount, 4);

        $this->averageSlotOpportunities = round($averageSlotOpportunities);
        $this->averageOpportunityFillRate = round($averageOpportunityFillRate, 4);
        $this->averageBilledAmount = round($averageBilledAmount, 4);

        $this->inBannerRequests = $inBannerRequests;
        $this->inBannerTimeouts = $inBannerTimeouts;
        $this->inBannerBilledAmount = $inBannerBilledAmount;
        $this->inBannerImpressions = $inBannerImpressions;

        $this->averageInBannerRequests = $averageInBannerRequests;
        $this->averageInBannerTimeouts = $averageInBannerTimeouts;
        $this->averageInBannerBilledAmount = round($averageInBannerBilledAmount, 4);
        $this->averageInBannerImpressions = $averageInBannerImpressions;
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
    public function getOpportunityFillRate()
    {
        return $this->opportunityFillRate;
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

    /**
     * @return float
     */
    public function getAverageOpportunityFillRate()
    {
        return $this->averageOpportunityFillRate;
    }

    /**
     * @return float
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    /**
     * @return mixed
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    /**
     * @return mixed
     */
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    /**
     * @return mixed
     */
    public function getInBannerBilledRate()
    {
        return $this->inBannerBilledRate;
    }

    /**
     * @return mixed
     */
    public function getInBannerBilledAmount()
    {
        return $this->inBannerBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerRequests()
    {
        return $this->averageInBannerRequests;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerImpressions()
    {
        return $this->averageInBannerImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerTimeouts()
    {
        return $this->averageInBannerTimeouts;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerBilledRate()
    {
        return $this->averageInBannerBilledRate;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerBilledAmount()
    {
        return $this->averageInBannerBilledAmount;
    }
}