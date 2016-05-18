<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\ComparisonReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface as BaseReportSelectorInterface;
interface ReportSelectorInterface extends BaseReportSelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return ComparisonReportInterface[]
     */
    public function getDiscrepancies(ReportTypeInterface $reportType, Params $params);
}