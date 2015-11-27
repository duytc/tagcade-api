<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection as BaseReportCollection;

class UnifiedReportCollection extends BaseReportCollection implements UnifiedReportResultInterface
{

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, array $reports, $name = null)
    {
       parent::__construct($reportType, $startDate, $endDate, $reports, $name);
    }
}