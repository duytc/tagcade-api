<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Result\Group;

use ArrayIterator;
use DateTime;
use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;

class ReportGroup implements ReportDataInterface, ReportResultInterface
{
    protected $reportType;
    protected $reports;
    protected $name;
    protected $startDate;
    protected $endDate;
    protected $fillRate;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;

    protected $averageOpportunities;
    protected $averageImpressions;
    protected $averageFillRate;
    protected $averageEarnedAmount;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param string $name
     * @param int $opportunities
     * @param int $impressions
     * @param float $fillRate
     * @param float $earnedAmount
     * @param float $averageOpportunities
     * @param float $averageImpressions
     * @param float $averageFillRate
     * @param float $averageEarnedAmount
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name,
        $opportunities, $impressions, $fillRate, $earnedAmount, $averageOpportunities, $averageImpressions, $averageFillRate, $averageEarnedAmount
    )
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->name = $name;
        $this->opportunities = $opportunities;
        $this->impressions = $impressions;
        $this->fillRate = $fillRate;
        $this->earnedAmount = $earnedAmount;

        $this->averageOpportunities = round($averageOpportunities);
        $this->averageImpressions = round($averageImpressions);
        $this->averageFillRate = round($averageFillRate, 4);
        $this->averageEarnedAmount = round($averageEarnedAmount, 4);
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

    /**
     * @return int
     */
    public function getOpportunities()
    {
        return $this->opportunities;
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
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return float
     */
    public function getEarnedAmount()
    {
        return $this->earnedAmount;
    }


    /**
     * @return float
     */
    public function getAverageOpportunity()
    {
        return $this->averageOpportunities;
    }

    /**
     * @return float
     */
    public function getAverageImpression()
    {
        return $this->averageImpressions;
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
    public function getAverageEarnedAmount()
    {
        return $this->averageEarnedAmount;
    }
}