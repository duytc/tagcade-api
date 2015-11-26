<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors;


use Tagcade\Model\Report\UnifiedReport\UnifiedReportModelInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector as AbstractUnifiedReportSelector;

class AdNetworkPartnerSelector extends AbstractUnifiedReportSelector
{
    public function getReports()
    {
        /** @var UnifiedReportModelInterface[] $reports */
        $reports = $this->repositories[0]->getReports();

        return $reports;
    }
}