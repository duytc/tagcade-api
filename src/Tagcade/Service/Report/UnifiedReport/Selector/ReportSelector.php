<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue as AverageValueDTO;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AbstractAccountManagement;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\Daily;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Grouper\ReportGrouperInterface;
use Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint\PulsePointRevenueReportGroup;
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
        /**
         * @var SlidingPagination $pagination
         */
        $pagination = $reports['pagination'];
        /**
         * @var AverageValueDTO $avg
         */
        $avg = $reports['avg'];

        if (!$pagination instanceof SlidingPagination
            || !$avg instanceof AverageValueDTO
        ) {
            return false;
        }

        if ($reportType instanceof AbstractAccountManagement || $reportType instanceof Daily) {
            return new PulsePointRevenueReportGroup($reportType, $params->getStartDate(), $params->getEndDate(), $pagination, null, $avg);
        }

        return new UnifiedReportCollection($reportType, $params->getStartDate(), $params->getEndDate(), $pagination, null, $avg);
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