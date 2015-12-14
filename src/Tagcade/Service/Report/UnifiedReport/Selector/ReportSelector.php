<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AbstractAccountManagement;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint\PulsePointRevenueReportGroup;
use Tagcade\Service\Report\UnifiedReport\Result\UnifiedReportCollection;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportSelectorInterface as UnifiedReportSelectorInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;


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

        if (!array_key_exists(AbstractReportRepository::REPORT_PAGINATION_RECORDS, $reports)
            || !array_key_exists(AbstractReportRepository::REPORT_TOTAL_RECORDS, $reports)
            || !array_key_exists(AbstractReportRepository::REPORT_AVERAGE_VALUES, $reports)
        ) {
            return false;
        }

        if ($reportType instanceof AbstractAccountManagement || $reportType instanceof Daily) {
            return new PulsePointRevenueReportGroup($reportType, $params->getStartDate(), $params->getEndDate(),
                $reports[AbstractReportRepository::REPORT_PAGINATION_RECORDS], $reports[AbstractReportRepository::REPORT_TOTAL_RECORDS],
                null, $reports[AbstractReportRepository::REPORT_AVERAGE_VALUES]);
        }

        return new UnifiedReportCollection($reportType, $params->getStartDate(), $params->getEndDate(),
            $reports[AbstractReportRepository::REPORT_PAGINATION_RECORDS], $reports[AbstractReportRepository::REPORT_TOTAL_RECORDS],
            null, $reports[AbstractReportRepository::REPORT_AVERAGE_VALUES]);
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