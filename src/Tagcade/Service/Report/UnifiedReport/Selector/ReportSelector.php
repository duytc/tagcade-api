<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportSelectorInterface as UnifiedReportSelectorInterface;

class ReportSelector implements UnifiedReportSelectorInterface
{
    /** @var SelectorInterface[] */
    protected $selectors = [];

    function __construct($selectors)
    {
        foreach ($selectors as $selector) {
            if (!$selector instanceof SelectorInterface) {
                continue;
            }

            $this->addSelector($selector);
        }
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

        return $reports;
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

        throw new NotSupportedException(sprintf('Not found any selector that supports this report type %s', $reportType));
    }
}