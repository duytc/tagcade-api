<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetworkDomainAdTag as AdNetworkDomainAdTagReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\NetworkDomainAdTag as UnifiedAdNetworkDomainAdTagSelector;

class AdNetworkDomainAdTag extends UnifiedAdNetworkDomainAdTagSelector
{
    /**
     * @var AdNetworkDomainAdTagReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkDomainAdTagReportType;
    }
}