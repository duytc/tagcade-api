<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Service\Report\UnifiedReport\Result\ReportCollection as BaseReportCollection;

class UnifiedReportCollection extends BaseReportCollection implements UnifiedReportResultInterface
{

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, SlidingPagination $pagination, $name = null)
    {
       parent::__construct($reportType, $startDate, $endDate, $pagination, $name);
    }
}