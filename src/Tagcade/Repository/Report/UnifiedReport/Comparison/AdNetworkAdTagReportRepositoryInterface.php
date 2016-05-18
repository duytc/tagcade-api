<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkAdTagReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface as UnifiedAdNetworkAdTagReportRepositoryInterface;

interface AdNetworkAdTagReportRepositoryInterface extends UnifiedAdNetworkAdTagReportRepositoryInterface
{
    /**
     * @param AdNetworkAdTagReportInterface $report
     * @return mixed
     */
    public function override(AdNetworkAdTagReportInterface $report);
}