<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;

abstract class AbstractSelector implements SelectorInterface
{
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        return $queryParams != null ? $this->doGetReports($reportType, $startDate, $endDate, $queryParams) : $this->doGetReports($reportType, $startDate, $endDate);
    }
}