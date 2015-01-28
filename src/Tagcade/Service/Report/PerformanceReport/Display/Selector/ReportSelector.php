<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\ReportGrouperInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection;
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
     * @param ReportGrouperInterface $reportGrouper
     */
    public function __construct(array $selectors, DateUtilInterface $dateUtil, ReportCreatorInterface $reportCreator, ReportGrouperInterface $reportGrouper)
    {
        foreach($selectors as $selector) {
            $this->addSelector($selector);
        }

        $this->reportCreator = $reportCreator;
        $this->dateUtil = $dateUtil;
        $this->reportGrouper = $reportGrouper;
    }

    public function addSelector(SelectorInterface $selector)
    {
        $this->selectors[] = $selector;
    }

    public function getReports(ReportTypeInterface $reportType, ParamsInterface $params)
    {
        $selector = $this->getSelectorFor($reportType);

        $todayIncludedInDateRange = $this->dateUtil->isTodayInRange($params->getStartDate(), $params->getEndDate());

        $reports = [];

        if ($todayIncludedInDateRange) {
            // Create today's report and add it to the first position in the array
            $reports[] = $this->reportCreator->getReport($reportType);
        }

        if ($this->dateUtil->isDateBeforeToday($params->getStartDate())) {
            // get historical reports only if the start date is before today's date

            $historicalEndDate = $params->getEndDate();

            if ($todayIncludedInDateRange) {
                // since today is in the date range and we are building that report with the report creator
                // set the end date to yesterday to make sure we do not query for the current day
                $historicalEndDate = new DateTime('yesterday');
            }

            $historicalReports = $selector->getReports($reportType, $params->getStartDate(), $historicalEndDate);

            $reports = array_merge($reports, $historicalReports);

            unset($historicalReports, $historicalEndDate);
        }

        if (empty($reports)) {
            return false;
        }

        $reportName = null;

        foreach($reports as $report) {
            /** @var ReportInterface $report */

            if (!$reportType->matchesReport($report)) {
                throw new LogicException('You tried to add reports to a collection that did not match the supplied report type');
            }

            if (null === $reportName) {
                $reportName = $report->getName();
            }

            unset($report);
        }

        $dates = array_map(function(ReportInterface $report) {
            return $report->getDate();
        }, $reports);

        // instead of using user-supplied dates for the collection date range
        // determine what the actual date range is

        $actualStartDate = min($dates);
        $actualEndDate = max($dates);

        $reportCollection = new ReportCollection($reportType, $actualStartDate, $actualEndDate, $reports, $reportName);

        unset($dates, $actualStartDate, $actualEndDate);

        $result = $reportCollection;

        if ($params->getGrouped()) {
            $result = $this->reportGrouper->groupReports($reportCollection);
        }

        return $result;
    }

    public function getGroupedReports(ReportTypeInterface $reportType, ParamsInterface $params)
    {
        $params->setGrouped(true);

        return $this->getReports($reportType, $params);
    }

    public function getMultipleReports(array $reportTypes, ParamsInterface $params)
    {
        $reports = [];

        foreach($reportTypes as $reportType) {
            if ($reportResult = $this->getReports($reportType, $params)) {
                $reports[] = $reportResult;
            }

            unset($reportResult);
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($reportTypes, $params->getStartDate(), $params->getEndDate(), $reports);

        $result = $reportCollection;

        if ($params->getGrouped()) {
            $result = $this->reportGrouper->groupReports($reportCollection);
        }

        return $result;
    }

    public function getMultipleGroupedReports(array $reportTypes, ParamsInterface $params)
    {
        $params->setGrouped(true);

        return $this->getMultipleReports($reportTypes, $params);
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