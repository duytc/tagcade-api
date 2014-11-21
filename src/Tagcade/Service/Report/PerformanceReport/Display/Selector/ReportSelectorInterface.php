<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;

interface ReportSelectorInterface
{
    /**
     * Get reports for one report type over a date range
     *
     * i.e, a report for a single ad network over a date range
     *
     * @param ReportTypeInterface $reportType
     * @param null $startDate
     * @param null $endDate
     * @param bool $group
     * @param bool $expand
     * @return ReportCollection|ReportGroup
     */
    public function getReports(ReportTypeInterface $reportType, $startDate = null, $endDate = null, $group = false, $expand = false);

    /**
     * Get reports for multiple report types over a date range
     *
     * i.e a report for multiple or all ad networks over a date range
     *
     * @param ReportTypeInterface[] $reportTypes
     * @param null $startDate
     * @param null $endDate
     * @param bool $group Group the results into one report with aggregated/averaged values
     * @param bool $expand Expand the results into their sub reports, i.e expand a site report into ad slot reports
     *                     This option has no effect if group is true, it will take priority
     * @return ReportCollection[]|ReportGroup[]
     */
    public function getMultipleReports(array $reportTypes, $startDate = null, $endDate = null, $group = false, $expand = false);
}