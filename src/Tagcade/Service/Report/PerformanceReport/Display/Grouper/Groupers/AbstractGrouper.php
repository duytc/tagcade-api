<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use DateTime;

/**
 * A grouper is only designed to be run once, if you need to group multiple sets of reports
 * you need to create a new grouper for each
 *
 * if you use this service in the symfony DI container you need to change the mode since
 * by default symfony creates 1 instance of a service and returns it each time.
 *
 * For this service, we need to create a new one and return it every time the service is requested
 */

abstract class AbstractGrouper implements GrouperInterface
{
    use CalculateRatiosTrait;
    use CalculateWeightedValueTrait;
    use CalculateRevenueTrait;

    private $reportType;
    private $reports;
    private $reportName;
    private $startDate;
    private $endDate;
    private $totalOpportunities;
    private $impressions;
    private $passbacks;
    private $fillRate;
    private $estCpm;
    private $estRevenue;
    private $averageTotalOpportunities;
    private $averageImpressions;
    private $averagePassbacks;
    private $averageEstCpm;
    private $averageEstRevenue;

    private $totalEstCpm;
    /**
     * @param ReportCollection $reportCollection
     */
    public function __construct(ReportCollection $reportCollection)
    {
        $reports = $reportCollection->getReports();
        $this->reports = $reports;

        if (empty($reports)) {
            throw new InvalidArgumentException('Expected a non-empty array of reports');
        }

        $this->reportType = $reportCollection->getReportType();
        $this->reportName = $reportCollection->getName();

        $this->groupReports($reports);
    }

    /**
     * @inheritdoc
     */
    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReportType(),
            $this->getReports(),
            $this->getReportName(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getTotalOpportunities(),
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getEstCpm(),
            $this->getEstRevenue(),
            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue()
        );
    }

    /**
     * @param ReportInterface[] $reports
     */
    private function groupReports(array $reports)
    {
        $dates = array_map(function(ReportInterface $report) {
            return $report->getDate();
        }, $reports);

        $startDate = min($dates);
        $endDate = max($dates);

        if ((!$startDate instanceof DateTime) || (!$endDate instanceof DateTime)) {
            throw new InvalidArgumentException('invalid date range for report group');
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;

        unset($dates, $startDate, $endDate);

        foreach($reports as $report) {
            $this->doGroupReport($report);
        }

        $this->setFillRate();
        $this->estCpm = $this->$this->calculateWeightedValue($reports, $frequency = 'estCpm', $weight = 'estRevenue');

        // Calculate average for totalOpportunities,impressions and passbacks
        $reportCount = count($this->getReports());
        $this->averageTotalOpportunities = $this->getRatio($this->getTotalOpportunities(), $reportCount);
        $this->averageImpressions = $this->getRatio($this->getImpressions(), $reportCount);
        $this->averagePassbacks = $this->getRatio($this->getPassbacks(), $reportCount);
        $this->averageEstCpm = $this->getRatio($this->getTotalEstCpm(), $reportCount);
        $this->averageEstRevenue = $this->getRatio($this->getEstRevenue(), $reportCount);
    }

    protected function addTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities += (int) $totalOpportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int) $impressions;
    }

    protected function addPassbacks($passbacks)
    {
        $this->passbacks += (int) $passbacks;
    }

    protected function addEstRevenue($estRevenue)
    {
        $this->estRevenue += (float) $estRevenue;
    }

    protected function addTotalEstCpm($estCpm)
    {
        $this->totalEstCpm += (float) $estCpm;
    }

    protected function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    protected function doGroupReport(ReportInterface $report)
    {
        $this->addTotalOpportunities($report->getTotalOpportunities());
        $this->addImpressions($report->getImpressions());
        $this->addPassbacks($report->getPassbacks());
        $this->addTotalEstCpm($report->getEstCpm());
        $this->addEstRevenue($report->getEstRevenue());
    }

    /**
     * @inheritdoc
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @inheritdoc
     */
    public function getReportName()
    {
        return $this->reportName;
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
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @inheritdoc
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @inheritdoc
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @inheritdoc
     */
    public function getAverageTotalOpportunities()
    {
        return $this->averageTotalOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    /**
     * @inheritdoc
     */
    public function getAveragePassbacks()
    {
        return $this->averagePassbacks;
    }

    /**
     * @inheritdoc
     */
    public function getEstCpm()
    {
        return $this->estCpm;
    }

    /**
     * @inheritdoc
     */
    public function getEstRevenue()
    {
        return $this->estRevenue;
    }

    /**
     * @inheritdoc
     */
    public function getAverageEstCpm()
    {
        return $this->averageEstCpm;
    }

    /**
     * @inheritdoc
     */
    public function getAverageEstRevenue()
    {
        return $this->averageEstRevenue;
    }

    /**
     * @return mixed
     */
    public function getTotalEstCpm()
    {
        return $this->totalEstCpm;
    }




}