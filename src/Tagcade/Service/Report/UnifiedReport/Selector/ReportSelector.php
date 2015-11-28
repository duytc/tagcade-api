<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Grouper\ReportGrouperInterface;
use Tagcade\Service\Report\UnifiedReport\Result\UnifiedReportCollection;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportSelectorInterface as UnifiedReportSelectorInterface;

class ReportSelector implements UnifiedReportSelectorInterface
{
    /** @var SelectorInterface[] */
    protected $selectors = [];
    /**
     * @var ReportGrouperInterface
     */
    private $reportGrouper;

    function __construct($selectors, ReportGrouperInterface $reportGrouper)
    {
        foreach ($selectors as $selector) {
            if (!$selector instanceof SelectorInterface) {
                continue;
            }

            $this->addSelector($selector);
        }

        $this->reportGrouper = $reportGrouper;
    }

    /**
     * @inheritdoc
     */
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        /**
         * @var SelectorInterface $selector
         */
        $selector = $this->getSelectorFor($reportType);

        $reports = $selector->getReports($reportType, $params);

        $result = new UnifiedReportCollection($reportType, $params->getStartDate(), $params->getEndDate(), $reports);

        if ($params->getGrouped()) {
            $result = $this->reportGrouper->groupReports($result);
        }

        return $result;
    }

    protected function addSelector(SelectorInterface $selector)
    {
        if (!in_array($selector, $this->selectors)) {
            $this->selectors [] = $selector;
        }

        return $this;
    }

    /**
     * get Selector For a report Type
     * @param $reportType
     * @return UnifiedReportSelectorInterface
     * @throws NotSupportedException
     */
    private function getSelectorFor(ReportTypeInterface $reportType)
    {
        foreach ($this->selectors as $selector) {
            /**
             * @var SelectorInterface $selector
             */
            if ($selector->supportReport($reportType)) {
                return $selector;
            }
        }

        throw new NotSupportedException(sprintf('Not found any selector that supports this report type %s', get_class($reportType)));
    }
}