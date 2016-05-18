<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors;


use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\SelectorInterface as BaseSelectorInterface;

interface SelectorInterface extends BaseSelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params);
}