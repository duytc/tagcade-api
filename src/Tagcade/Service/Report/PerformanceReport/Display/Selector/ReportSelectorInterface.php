<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface ReportSelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param null $startDate
     * @param null $endDate
     * @param bool $expand
     * @return ReportInterface[]
     */
    public function getReports(ReportTypeInterface $reportType, $startDate = null, $endDate = null, $expand = false);
}