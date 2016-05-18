<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface as UnifiedAdNetworkDomainReportRepositoryInterface;

interface AdNetworkDomainReportRepositoryInterface extends UnifiedAdNetworkDomainReportRepositoryInterface
{
    /**
     * @param AdNetworkDomainReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkDomainReportInterface $report);
}