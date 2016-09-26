<?php

namespace Tagcade\Model\Report\VideoReport\ReportType;

use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface ReportTypeInterface
{
    /**
     * @return string|null
     */
    public function getReportType();

    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report);

    /**
     * check if Supports Params and Breakdowns
     *
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function isSupportParams(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

    /**
     * check if supports min break down for this report type
     * @return mixed
     */
    public function supportMinBreakDown();

    /**
     * Get id of video objec id (video ad source, video ad tag, video demand partner, publisher)
     * @return mixed
     */
    public function getVideoObjectId();

}