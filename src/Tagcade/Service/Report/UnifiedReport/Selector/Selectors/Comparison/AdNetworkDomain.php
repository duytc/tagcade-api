<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetworkDomain as AdNetworkDomainReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\NetworkSite as UnifiedAdNetworkDomainSelector;

class AdNetworkDomain extends UnifiedAdNetworkDomainSelector
{
    /**
     * @var AdNetworkDomainReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkDomainReportType;
    }
}