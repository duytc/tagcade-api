<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;
use Tagcade\Service\DateUtilInterface;
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
     * @param SelectorInterface[] $selectors
     * @param DateUtilInterface $dateUtil
     * @param ReportCreatorInterface $reportCreator
     */
    public function __construct(array $selectors, DateUtilInterface $dateUtil, ReportCreatorInterface $reportCreator)
    {
        foreach($selectors as $selector) {
            $this->addSelector($selector);
        }

        $this->reportCreator = $reportCreator;
        $this->dateUtil = $dateUtil;
    }

    public function addSelector(SelectorInterface $selector)
    {
        $this->selectors[] = $selector;
    }

    /**
     * @inheritdoc
     */
    public function getReports(ReportTypeInterface $reportType, $startDate = null, $endDate = null, $expand = false)
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

        if ($expand) {
            // if the expand option is true, instead of returning the report, we return an array of its sub reports
            foreach($reports as &$report) { // notice the &$report reference
                if (!$report instanceof SuperReportInterface) {
                    continue;
                }

                $report = $report->getSubReports()->toArray();
            }
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