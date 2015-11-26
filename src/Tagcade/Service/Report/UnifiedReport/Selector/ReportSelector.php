<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Exception\NotSupportedException;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportSelectorInterface as UnifiedReport_ReportSelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AdNetworkPartnerSelector;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AdTagSelector;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\SelectorInterface as UnifiedReportSelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\SiteSelector;

class ReportSelector implements UnifiedReport_ReportSelectorInterface
{
    protected $selectors;

    function __construct($selectors)
    {
        $this->selectors = $selectors;
    }

    /**
     * @inheritdoc
     */
    public function getReports($reportType, $params)
    {
        $selector = $this->getSelectorFor($reportType);

        $reports = $selector->getReports($reportType, $params);

        return $reports;
    }

    /**
     * get Selector For a report Type
     * @param $reportType
     * @return UnifiedReportSelectorInterface
     * @throws NotSupportedException
     */
    private function getSelectorFor($reportType)
    {
        if ($reportType === 1) {
            return AdNetworkPartnerSelector();
        } elseif ($reportType === 2) {
            return new AdTagSelector([]);
        } elseif ($reportType === 3) {
            return new SiteSelector([]);
        }

        throw new NotSupportedException(sprintf('Not supported report type %s', $reportType));
    }
}