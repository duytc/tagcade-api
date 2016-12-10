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

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner as PartnerReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher as SubPublisherReportType;

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
     * initialize $reportCreator as null when we do not need to create new reports, when get unified reports ie.
     * @param SelectorInterface[] $selectors
     * @param DateUtilInterface $dateUtil
     * @param ReportGrouperInterface $reportGrouper
     * @param ReportCreatorInterface|null $reportCreator
     */
    public function __construct(array $selectors, DateUtilInterface $dateUtil, ReportGrouperInterface $reportGrouper, $reportCreator = null)
    {
        foreach($selectors as $selector) {
            $this->addSelector($selector);
        }

        if ($reportCreator instanceof ReportCreatorInterface) {
            $this->reportCreator = $reportCreator;
        }

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
        $yesterdayIncludedInDateRange = $this->dateUtil->isYesterdayInRange($params->getStartDate(), $params->getEndDate());

        $reports = [];

        if ($todayIncludedInDateRange && $this->reportCreator instanceof ReportCreatorInterface) {
            if (
                !$reportType instanceof PartnerReportType\Account &&
                !$reportType instanceof PartnerReportType\AdNetworkAdTag &&
                !$reportType instanceof PartnerReportType\AdNetworkDomain &&
                !$reportType instanceof PartnerReportType\AdNetworkDomainAdTagSubPublisher &&
                !$reportType instanceof SubPublisherReportType\SubPublisher &&
                !$reportType instanceof SubPublisherReportType\SubPublisherAdNetwork
            ) {
                // the report types above do not have creator, they're derived from other reports
                // Create today's report and add it to the first position in the array
                $reports[] = $this->reportCreator->getReport($reportType);
            }
        }

        if ($this->dateUtil->isDateBeforeToday($params->getStartDate())) {
            // get historical reports only if the start date is before today's date

            $historicalEndDate = $params->getEndDate();

            if ($yesterdayIncludedInDateRange) {
                // since today is in the date range and we are building that report with the report creator
                // set the end date to yesterday to make sure we do not query for the current day
                $historicalEndDate = new DateTime('yesterday');
                $yesterdayReport = $selector->getReports($reportType, $historicalEndDate, $historicalEndDate, $params->getQueryParams());
                if ($yesterdayReport === false || empty($yesterdayReport)) {
                    $this->reportCreator->setDate($historicalEndDate);
                    $reports[] = $this->reportCreator->getReport($reportType);
                } else {
                    $reports = array_merge($reports, $yesterdayReport);
                }
            }

            $historicalReports = [];
            if ($yesterdayIncludedInDateRange) {
                $dayBeforeYesterday = date_create($historicalEndDate->format('Y-m-d'))->modify('-1 day');

                if ($dayBeforeYesterday >= $params->getStartDate()) {
                    $params->setDateRange($params->getStartDate(), $dayBeforeYesterday);

                    $historicalReports = $selector->getReports($reportType, $params->getStartDate(), $params->getEndDate(), $params->getQueryParams());
                }
            } else {
                if ($params->getEndDate() >= $params->getStartDate()) {
                    $historicalReports = $selector->getReports($reportType, $params->getStartDate(), $params->getEndDate(), $params->getQueryParams());
                }
            }

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