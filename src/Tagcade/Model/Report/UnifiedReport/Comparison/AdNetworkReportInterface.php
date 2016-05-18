<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface as PerformanceAdNetworkReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkReportInterface as UnifiedAdNetworkReportInterface;

interface AdNetworkReportInterface extends ComparisonReportInterface
{
    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @return PerformanceAdNetworkReportInterface
     */
    public function getPerformanceAdNetworkReport();

    /**
     * @param PerformanceAdNetworkReportInterface $performanceAdNetworkReport
     * @return self
     */
    public function setPerformanceAdNetworkReport($performanceAdNetworkReport);

    /**
     * @return UnifiedAdNetworkReportInterface
     */
    public function getUnifiedAdNetworkReport();

    /**
     * @param UnifiedAdNetworkReportInterface $unifiedAdNetworkReport
     * @return self
     */
    public function setUnifiedAdNetworkReport($unifiedAdNetworkReport);
}