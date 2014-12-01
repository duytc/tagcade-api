<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportResultInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class ReportGroup
{
    private $reportType;
    private $reports;
    private $name;
    private $startDate;
    private $endDate;
    private $fillRate;

    /**
     * @var int
     */
    private $totalOpportunities;
    private $impressions;
    private $passbacks;

    /**
     * @var float
     */
    private $estCpm;
    /**
     * @var float
     */
    private $estRevenue;

    private $averageTotalOpportunities;
    private $averageImpressions;
    private $averagePassbacks;

    /**
     * @var float
     */
    private $averageEstCpm;
    /**
     * @var float
     */
    private $averageEstRevenue;
    /**
     * @param ReportTypeInterface $reportType
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
     */
    public function __construct(ReportTypeInterface $reportType, array $reports, $name, DateTime $startDate, DateTime $endDate,
        $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue
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
        $this->estCpm = $estCpm;
        $this->estRevenue = $estRevenue;

        $this->averageTotalOpportunities = round($averageTotalOpportunities);
        $this->averageImpressions = round($averageImpressions);
        $this->averagePassbacks = round($averagePassbacks);
        $this->averageEstCpm = round($averageEstCpm, 4);
        $this->averageEstRevenue = round($averageEstRevenue, 4);
    }

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @inheritdoc
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


}