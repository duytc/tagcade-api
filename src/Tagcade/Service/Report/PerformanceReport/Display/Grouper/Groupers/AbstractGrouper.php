<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Collection;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
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

    private $reportType;
    private $reportName;
    private $startDate;
    private $endDate;
    private $totalOpportunities;
    private $impressions;
    private $passbacks;
    private $fillRate;

    /**
     * @param Collection $reportCollection
     */
    public function __construct(Collection $reportCollection)
    {
        $reports = $reportCollection->getReports();

        if (empty($reports)) {
            throw new InvalidArgumentException('Expected a non-empty array of reports');
        }

        $this->reportType = $reportCollection->getReportType();
        $this->reportName = $reportCollection->getReportName();

        $this->groupReports($reports);
    }

    /**
     * @inheritdoc
     */
    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReportType(),
            $this->getReportName(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getTotalOpportunities(),
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate()
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
}