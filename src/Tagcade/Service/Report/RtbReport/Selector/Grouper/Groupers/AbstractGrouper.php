<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Grouper\Groupers;

use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;


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
    use CalculateRevenueTrait;

    private $reportType;
    private $reports;
    private $reportName;
    private $startDate;
    private $endDate;
    private $opportunities;
    private $impressions;
    private $fillRate;
    private $earnedAmount;
    private $averageOpportunities;
    private $averageImpressions;
    private $averageFillRate;
    private $averageEarnedAmount;

    /**
     * @param ReportResultInterface $reportResult
     */
    public function __construct(ReportResultInterface $reportResult)
    {
        $reports = $reportResult->getReports();

        if (empty($reports)) {
            throw new InvalidArgumentException('Expected a non-empty array of reports');
        }

        $this->reportType = $reportResult->getReportType();
        $this->reportName = $reportResult->getName();
        $this->startDate = $reportResult->getStartDate();
        $this->endDate = $reportResult->getEndDate();
        $this->reports = $reports;

        $this->groupReports($reports);
    }

    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getReportName(),
            $this->getOpportunities(),
            $this->getImpressions(),
            $this->getFillRate(),
            $this->getEarnedAmount(),
            $this->getAverageOpportunities(),
            $this->getAverageImpressions(),
            $this->getAverageFillRate(),
            $this->getAverageEarnedAmount()
        );
    }

    /**
     * @param ReportDataInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        foreach($reports as $report) {
            $this->doGroupReport($report);
        }

        $this->setFillRate();

        // Calculate average for opportunity,impression
        $reportCount = count($this->getReports());
        $this->averageOpportunities = $this->getRatio($this->getOpportunities(), $reportCount);
        $this->averageImpressions = $this->getRatio($this->getImpressions(), $reportCount);
        $this->averageFillRate = $this->getRatio($this->getFillRate(), $reportCount);
        $this->averageEarnedAmount = $this->getRatio($this->getEarnedAmount(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        $this->addOpportunities($report->getOpportunities());
        $this->addImpressions($report->getImpressions());
        $this->addFillRate($report->getFillRate());
        $this->addEarnedAmount($report->getEarnedAmount());
    }

    protected function addOpportunities($opportunities)
    {
        $this->opportunities += (int) $opportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int) $impressions;
    }

    protected function addFillRate($fillRate)
    {
        $this->fillRate += (float) $fillRate;
    }

    protected function addEarnedAmount($earnedAmount)
    {
        $this->earnedAmount += (float)$earnedAmount;
    }

    protected function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getOpportunities());
    }

    public function getReportType()
    {
        return $this->reportType;
    }

    public function getReports()
    {
        return $this->reports;
    }

    public function getReportName()
    {
        return $this->reportName;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getOpportunities()
    {
        return $this->opportunities;
    }

    public function getImpressions()
    {
        return $this->impressions;
    }

    public function getFillRate()
    {
        return $this->fillRate;
    }

    public function getEarnedAmount()
    {
        return $this->earnedAmount;
    }

    public function getAverageOpportunities()
    {
        return $this->averageOpportunities;
    }

    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    public function getAverageEarnedAmount()
    {
        return $this->averageEarnedAmount;
    }
}