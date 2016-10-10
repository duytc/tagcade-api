<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;

interface SelectorInterface
{
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null);

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}