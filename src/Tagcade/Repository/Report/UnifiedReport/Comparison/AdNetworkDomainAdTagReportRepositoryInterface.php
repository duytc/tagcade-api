<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface as UnifiedAdNetworkDomainAdTagReportRepositoryInterface;

interface AdNetworkDomainAdTagReportRepositoryInterface extends UnifiedAdNetworkDomainAdTagReportRepositoryInterface
{
    /**
     * @param AdNetworkDomainAdTagReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainAdTagReportInterface $report);
}