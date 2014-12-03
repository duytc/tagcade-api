<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class ReportGroup implements ReportDataInterface
{
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

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param ReportInterface[] $reports
     * @param string $name
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $totalOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     * @param float $estCpm
     * @param float $estRevenue
     * @param float $averageTotalOpportunities
     * @param float $averageImpressions
     * @param float $averagePassbacks
     * @param float $averageEstCpm
     * @param float $averageEstRevenue
     * @param float $averageFillRate
     */
    public function __construct($reportType, array $reports, $name, DateTime $startDate, DateTime $endDate,
        $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate
    )
    {
        $this->reportType = $reportType;
        $this->reports = $reports;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalOpportunities = $totalOpportunities;
        $this->impressions = $impressions;
        $this->passbacks = $passbacks;
        $this->fillRate = $fillRate;
        $this->estCpm = round($estCpm, 4);
        $this->estRevenue = round($estRevenue, 4);

        $this->averageTotalOpportunities = round($averageTotalOpportunities);
        $this->averageImpressions = round($averageImpressions);
        $this->averagePassbacks = round($averagePassbacks);
        $this->averageEstCpm = round($averageEstCpm, 4);
        $this->averageEstRevenue = round($averageEstRevenue, 4);
        $this->averageFillRate = round($averageFillRate, 4);
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
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
}