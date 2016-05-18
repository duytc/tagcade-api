<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepositoryInterface as UnifiedAdNetworkDomainAdTagSubPublisherReportRepositoryInterface;

interface AdNetworkDomainAdTagSubPublisherReportRepositoryInterface extends UnifiedAdNetworkDomainAdTagSubPublisherReportRepositoryInterface
{
    /**
     * @param AdNetworkDomainAdTagSubPublisherReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainAdTagSubPublisherReportInterface $report);
}