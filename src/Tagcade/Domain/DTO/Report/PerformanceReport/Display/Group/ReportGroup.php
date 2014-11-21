<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class ReportGroup
{
    private $reportType;
    private $name;
    private $startDate;
    private $endDate;
    private $totalOpportunities;
    private $impressions;
    private $passbacks;
    private $fillRate;

    /**
     * @param ReportTypeInterface $reportType
     * @param string $name
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $totalOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     */
    public function __construct(ReportTypeInterface $reportType, $name, DateTime $startDate, DateTime $endDate, $totalOpportunities, $impressions, $passbacks, $fillRate)
    {
        $this->reportType = $reportType;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalOpportunities = $totalOpportunities;
        $this->impressions = $impressions;
        $this->passbacks = $passbacks;
        $this->fillRate = $fillRate;
    }

    /**
     * @return ReportTypeInterface
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