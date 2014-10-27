<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

interface SelectorInterface
{
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate);

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}