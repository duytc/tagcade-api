<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportResultInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class ReportGroup implements ReportResultInterface
{
    private $reportType;
    private $reports;
    private $name;
    private $startDate;
    private $endDate;
    private $totalOpportunities;
    private $impressions;
    private $passbacks;
    private $fillRate;

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
     */
    public function __construct(ReportTypeInterface $reportType, array $reports, $name, DateTime $startDate, DateTime $endDate, $totalOpportunities, $impressions, $passbacks, $fillRate)
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
}