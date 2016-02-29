<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

interface SelectorInterface
{
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate);

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}