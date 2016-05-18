<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportInterface as PerformanceAdNetworkAdTagReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagReportInterface as UnifiedAdNetworkAdTagReportInterface;

interface AdNetworkAdTagReportInterface extends ComparisonReportInterface
{
    /**
     * @return string
     */
    public function getPartnerTagId();

    /**
     * @param string $partnerTagId
     * @return self
     */
    public function setPartnerTagId($partnerTagId);

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
     * @return PerformanceAdNetworkAdTagReportInterface
     */
    public function getPerformanceAdNetworkAdTagReport();

    /**
     * @param PerformanceAdNetworkAdTagReportInterface $performanceAccountReport
     * @return self
     */
    public function setPerformanceAdNetworkAdTagReport($performanceAccountReport);

    /**
     * @return UnifiedAdNetworkAdTagReportInterface
     */
    public function getUnifiedAdNetworkAdTagReport();

    /**
     * @param UnifiedAdNetworkAdTagReportInterface $unifiedAccountReport
     * @return self
     */
    public function setUnifiedAdNetworkAdTagReport($unifiedAccountReport);
}