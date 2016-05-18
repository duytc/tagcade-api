<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetworkAdTag as AdNetworkAdTagReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkAdTagReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\NetworkAdTag as UnifiedAdNetworkAdTagSelector;

class AdNetworkAdTag extends UnifiedAdNetworkAdTagSelector
{
    /**
     * @var AdNetworkAdTagReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkAdTagReportType;
    }
}