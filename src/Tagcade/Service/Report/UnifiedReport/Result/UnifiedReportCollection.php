<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use Tagcade\Service\Report\UnifiedReport\Result\ReportCollection as BaseReportCollection;

class UnifiedReportCollection extends BaseReportCollection implements UnifiedReportResultInterface
{

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, $reports, $totalRecord, $name = null, $avg)
    {
       parent::__construct($reportType, $startDate, $endDate, $reports, $totalRecord, $name, $avg);
    }
}