<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepositoryInterface as UnifiedAdNetworkReportRepositoryInterface;

interface AdNetworkReportRepositoryInterface extends UnifiedAdNetworkReportRepositoryInterface
{
    /**
     * @param AdNetworkReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkReportInterface $report);
}