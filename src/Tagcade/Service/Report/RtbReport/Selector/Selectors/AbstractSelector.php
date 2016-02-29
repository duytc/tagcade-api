<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

abstract class AbstractSelector implements SelectorInterface
{
    public function getReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->doGetReports($reportType, $startDate, $endDate);
    }
}