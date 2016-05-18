<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetworkDomainAdTagSubPublisher as AdNetworkDomainAdTagSubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\NetworkDomainAdTagSubPublisher as UnifiedAdNetworkDomainAdTagSubPublisherSelector;

class AdNetworkDomainAdTagSubPublisher extends UnifiedAdNetworkDomainAdTagSubPublisherSelector
{
    /**
     * @var AdNetworkDomainAdTagSubPublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkDomainAdTagSubPublisherReportType;
    }
}