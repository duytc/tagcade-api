<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetwork as AdNetworkReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\Network as UnifiedNetworkSelector;

class AdNetwork extends UnifiedNetworkSelector
{
    /**
     * @var AdNetworkReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkReportType;
    }
}