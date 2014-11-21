<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Grouper\ReportGrouperInterface;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Collection;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\SelectorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use DateTime;

class ReportSelector implements ReportSelectorInterface
{
    /**
     * @var SelectorInterface[]
     */
    protected $selectors;

    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    /**
     * @var ReportCreatorInterface
     */
    protected $reportCreator;

    /**
     * @var ReportGrouperInterface
     */
    protected $reportGrouper;

    /**
     * @param SelectorInterface[] $selectors
     * @param DateUtilInterface $dateUtil
     * @param ReportCreatorInterface $reportCreator
     * @param ReportGrouperInterface $reportAggregator
     */
    public function __construct(array $selectors, DateUtilInterface $dateUtil, ReportCreatorInterface $reportCreator, ReportGrouperInterface $reportAggregator)
    {
        foreach($selectors as $selector) {
            $this->addSelector($selector);
        }

        $this->reportCreator = $reportCreator;
        $this->dateUtil = $dateUtil;
        $this->reportGrouper = $reportAggregator;
    }

    public function addSelector(SelectorInterface $selector)
    {
        $this->selectors[] = $selector;
    }

    /**
     * @inheritdoc
     */
    public function getReports(ReportTypeInterface $reportType, $startDate = null, $endDate = null, $group = false, $expand = false)
    {
        $selector = $this->getSelectorFor($reportType);

        $startDate = $this->dateUtil->getDateTime($startDate, true);
        $endDate = $this->dateUtil->getDateTime($endDate);

        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        $today = new DateTime('today');
        if ($startDate > $today || $endDate > $today) {
            throw new InvalidArgumentException('Can only get report information for reports older than today');
        }

        $todayIncludedInDateRange = $this->dateUtil->isTodayInRange($startDate, $endDate);

        $reports = [];

        if ($todayIncludedInDateRange) {
            // Create today's report and add it to the first position in the array
            $reports[] = $this->reportCreator->getReport($reportType);
        }

        if ($this->dateUtil->isDateBeforeToday($startDate)) {
            // get historical reports only if the start date is before today's date

            if ($todayIncludedInDateRange) {
                // since today is in the date range and we are building that report with the report creator
                // set the end date to yesterday to make sure we do not query for the current day
                $endDate = new DateTime('yesterday');
            }

            $historicalReports = $selector->getReports($reportType, $startDate, $endDate);

            $reports = array_merge($reports, $historicalReports);

            unset($historicalReports); // used a var here for clarity
        }

        if ($group) {
            $reports = $this->reportGrouper->groupReports(new Collection($reportType, $startDate, $endDate, $reports));
        } else if ($expand && $reportType->isExpandable()) {
            // do not allow both group and expand
            $reports = array_map(function(SuperReportInterface $report) {
                return $report->getSubReports()->toArray();
            }, $reports);
        }

        return $reports;
    }

    /**
     * @inheritdoc
     */
    public function getMultipleReports(array $reportTypes, $startDate = null, $endDate = null, $group = false, $expand = false)
    {
        $reports = [];

        foreach($reportTypes as $reportType) {
            $reports[] = $this->getReports($reportType, $startDate, $endDate, $group, $expand);
        }

        return $reports;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return SelectorInterface
     * @throws RunTimeException
     */
    protected function getSelectorFor(ReportTypeInterface $reportType)
    {
        foreach($this->selectors as $selector) {
            if ($selector->supportsReportType($reportType)) {
                return $selector;
            }
        }

        throw new RuntimeException('cannot find a selector for this report type');
    }
}