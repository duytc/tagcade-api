<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportModelInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\UnifiedReport\Result\Group\UnifiedReportGroup;


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

    protected $reportType;
    protected $reports;
    protected $reportName;
    protected $startDate;
    protected $endDate;

    // as total value
    protected $paidImps;
    protected $totalImps;

    // as weighted value
    protected $fillRate;

    // as average value
    protected $averageFillRate;
    protected $averageTotalImps;
    protected $averagePaidImps;

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
        return new UnifiedReportGroup(
            $this->reportType,
            $this->startDate,
            $this->endDate,
            $this->reports,
            $this->reportName,

            $this->paidImps,
            $this->totalImps,
            $this->fillRate,

            $this->averageFillRate,
            $this->averagePaidImps,
            $this->averageTotalImps
        );
    }

    /**
     * @return \Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @param PulsePointUnifiedReportModelInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        // do the total
        $totalFillRage = 0;

        foreach ($reports as $report) {
            $this->doGroupReport($report);

            $totalFillRage += $report->getFillRate();
        }

        // Calculate average
        $reportCount = count($this->getReports());
        $this->averageFillRate = $this->getRatio($totalFillRage, $reportCount);
        $this->averagePaidImps = $this->getRatio($this->paidImps, $reportCount);
        $this->averageTotalImps = $this->getRatio($this->totalImps, $reportCount);

        // Calculate weighted value for fillRate
        // TODO calculate fillRate using weighted value
        $this->fillRate = $this->calculateWeightedValue($reports, 'fillRate', 'paidImps');
    }

    protected function doGroupReport(PulsePointUnifiedReportModelInterface $report)
    {
        // for calculating total
        $this->addPaidImps($report->getPaidImps());
        $this->addTotalImps($report->getTotalImps());
    }

    protected function addPaidImps($paidImps)
    {
        $this->paidImps += (int)$paidImps;
    }

    protected function addTotalImps($totalImps)
    {
        $this->totalImps += (int)$totalImps;
    }
}