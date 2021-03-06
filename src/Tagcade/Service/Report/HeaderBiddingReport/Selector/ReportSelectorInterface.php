<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector;

use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;

interface ReportSelectorInterface
{
    /**
     * Get reports for one report type over a date range
     *
     * i.e, a report for a single ad network over a date range
     *
     * @param ReportTypeInterface $reportType
     * @param ParamsInterface $params
     * @return ReportResultInterface|false
     */
    public function getReports(ReportTypeInterface $reportType, ParamsInterface $params);

    /**
     * Convenience method to have a defined return type of ReportGroup
     *
     * @param ReportTypeInterface $reportType
     * @param ParamsInterface $params
     * @return ReportGroup|false
     */
    public function getGroupedReports(ReportTypeInterface $reportType, ParamsInterface $params);

    /**
     * Get reports for multiple report types over a date range
     *
     * i.e a report for multiple or all ad networks over a date range
     *
     * @param ReportTypeInterface[] $reportTypes
     * @param ParamsInterface $params
     * @return ReportResultInterface|false
     */
    public function getMultipleReports(array $reportTypes, ParamsInterface $params);

    /**
     * Convenience method to have a defined return type of ReportGroup[]
     *
     * @param array $reportTypes
     * @param ParamsInterface $params
     * @return ReportGroup|false
     */
    public function getMultipleGroupedReports(array $reportTypes, ParamsInterface $params);
}