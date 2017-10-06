<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use ArrayIterator;
use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

class ReportGroup implements ReportDataInterface, ReportResultInterface
{
    protected $reportType;
    protected $reports;
    protected $name;
    protected $date;
    protected $startDate;
    protected $endDate;
    protected $fillRate;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $estCpm;
    protected $estRevenue;
    protected $adOpportunities;
    protected $inBannerImpressions;
    protected $inBannerRequests;
    protected $inBannerTimeouts;

    protected $averageTotalOpportunities;
    protected $averageImpressions;
    protected $averagePassbacks;
    protected $averageEstCpm;
    protected $averageEstRevenue;
    protected $averageFillRate;
    protected $averageAdOpportunities;
    protected $averageInBannerImpressions;
    protected $averageInBannerRequests;
    protected $averageInBannerTimeouts;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param string $name
     * @param int $totalOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     * @param float $estCpm
     * @param float $estRevenue
     * @param int $adOpportunities
     * @param float $averageTotalOpportunities
     * @param float $averageImpressions
     * @param float $averagePassbacks
     * @param float $averageEstCpm
     * @param float $averageEstRevenue
     * @param float $averageFillRate
     * @param float $averageAdOpportunities
     * @param $inBannerImpressions
     * @param $inBannerRequests
     * @param $inBannerTimeouts
     * @param $averageInBannerImpressions
     * @param $averageInBannerRequests
     * @param $averageInBannerTimeouts
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name, $totalOpportunities, $impressions,
    $passbacks, $fillRate, $estCpm, $estRevenue, $adOpportunities, $averageTotalOpportunities, $averageImpressions, $averagePassbacks,
    $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageAdOpportunities, $inBannerImpressions, $inBannerRequests, $inBannerTimeouts,
    $averageInBannerImpressions, $averageInBannerRequests, $averageInBannerTimeouts
    )
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->name = $name;
        $this->totalOpportunities = $totalOpportunities;
        $this->impressions = $impressions;
        $this->passbacks = $passbacks;
        $this->fillRate = $fillRate;
        $this->estCpm = round($estCpm, 4);
        $this->estRevenue = round($estRevenue, 4);
        $this->adOpportunities = $adOpportunities;
        $this->inBannerTimeouts = $inBannerTimeouts;
        $this->inBannerRequests = $inBannerRequests;
        $this->inBannerImpressions = $inBannerImpressions;

        $this->averageTotalOpportunities = round($averageTotalOpportunities);
        $this->averageImpressions = round($averageImpressions);
        $this->averagePassbacks = round($averagePassbacks);
        $this->averageEstCpm = round($averageEstCpm, 4);
        $this->averageEstRevenue = round($averageEstRevenue, 4);
        $this->averageFillRate = round($averageFillRate, 4);
        $this->averageAdOpportunities = round($averageAdOpportunities);
        $this->averageInBannerTimeouts = round($averageInBannerTimeouts);
        $this->averageInBannerRequests = round($averageInBannerRequests);
        $this->averageInBannerImpressions = round($averageInBannerImpressions);
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return ReportDataInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return int
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @return int
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return float
     */
    public function getAverageTotalOpportunities()
    {
        return $this->averageTotalOpportunities;
    }

    /**
     * @return float
     */
    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    /**
     * @return float
     */
    public function getAveragePassbacks()
    {
        return $this->averagePassbacks;
    }

    /**
     * @return float
     */
    public function getEstCpm()
    {
        return $this->estCpm;
    }

    /**
     * @return float
     */
    public function getEstRevenue()
    {
        return $this->estRevenue;
    }

    /**
     * @return int
     */
    public function getAdOpportunities()
    {
        return $this->adOpportunities;
    }

    /**
     * @return float
     */
    public function getAverageEstCpm()
    {
        return $this->averageEstCpm;
    }

    /**
     * @return float
     */
    public function getAverageEstRevenue()
    {
        return $this->averageEstRevenue;
    }

    /**
     * @return float
     */
    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    /**
     * @return float
     */
    public function getAverageAdOpportunities()
    {
        return $this->averageAdOpportunities;
    }

    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }


}