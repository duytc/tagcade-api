<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

interface SelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param null|array|mixed $queryParams
     * @return array
     */
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null);

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}