<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\AdSlotReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class AdSlotReportGroup extends BilledReportGroup implements AdSlotReportDataInterface
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

    protected $slotOpportunities;

    // new properties
    protected $refreshedSlotOpportunities;
    protected $averageRefreshedSlotOpportunities;

    /**
     * BilledReportGroup constructor.
     * @param ReportTypeInterface|\Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $reports
     * @param string $name
     * @param int $totalOpportunities
     * @param int $slotOpportunities
     * @param int $refreshedSlotOpportunities
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
     * @param $averageRefreshedSlotOpportunities
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
                                $totalOpportunities, $slotOpportunities, $refreshedSlotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue, $adOpportunities, $opportunityFillRate,
                                $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities, $averageRefreshedSlotOpportunities, $averageBilledAmount,
                                $inBannerRequests, $inBannerTimeouts, $inBannerBilledAmount, $inBannerImpressions, $averageInBannerRequests, $averageInBannerTimeouts, $averageInBannerBilledAmount, $averageInBannerImpressions, $averageAdOpportunities, $averageOpportunityFillRate
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $slotOpportunities, $impressions, $passbacks, $fillRate, $billedAmount, $estCpm, $estRevenue, $adOpportunities, $opportunityFillRate,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageSlotOpportunities, $averageBilledAmount,
            $inBannerRequests, $inBannerTimeouts, $inBannerBilledAmount, $inBannerImpressions, $averageInBannerRequests, $averageInBannerTimeouts, $averageInBannerBilledAmount, $averageInBannerImpressions, $averageAdOpportunities, $averageOpportunityFillRate
        );

        $this->slotOpportunities = $slotOpportunities;
        $this->refreshedSlotOpportunities = $refreshedSlotOpportunities;


        $this->averageSlotOpportunities = round($averageSlotOpportunities);
        $this->averageRefreshedSlotOpportunities = round($averageRefreshedSlotOpportunities);
    }

    /**
     * @return int
     */
    public function getRefreshedSlotOpportunities()
    {
        return $this->refreshedSlotOpportunities;
    }

    /**
     * @return float
     */
    public function getAverageRefreshedSlotOpportunities()
    {
        return $this->averageRefreshedSlotOpportunities;
    }
}