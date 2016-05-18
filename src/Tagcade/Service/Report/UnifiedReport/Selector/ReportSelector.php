<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\ReportGrouperInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelector as BaseReportSelector;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\SelectorInterface;

class ReportSelector extends BaseReportSelector implements ReportSelectorInterface
{
    /**
     * @var SelectorInterface[]
     */
    protected $selectors;

    /**
     * ReportSelector constructor.
     * @param SelectorInterface[] $selectors
     * @param ReportGrouperInterface $reportGrouper
     * @param DateUtilInterface $dateUtil
     */
    public function __construct(array $selectors, ReportGrouperInterface $reportGrouper, DateUtilInterface $dateUtil)
    {
        parent::__construct($selectors, $dateUtil, $reportGrouper);
    }

    public function getDiscrepancies(ReportTypeInterface $reportType, Params $params)
    {
        $selector = $this->getSelectorFor($reportType);
        if (!$selector instanceof SelectorInterface) {
            throw new LogicException(sprintf('expect UnifiedSelectorInterface, %s given', get_class($selector)));
        }

        $report = $selector->getDiscrepancy($reportType, $params);

        if (!is_array($report) || empty($report)) {
            return false;
        }

        $reportCollection = new ReportCollection($reportType, $params->getStartDate(), $params->getEndDate(), $report, $reportType->getReportType());
        $result = $reportCollection;

        if ($params->getGrouped()) {
            $result = $this->reportGrouper->groupReports($reportCollection);
        }

        return $result;
    }
}