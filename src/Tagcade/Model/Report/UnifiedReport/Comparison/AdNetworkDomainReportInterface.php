<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportInterface as PerformanceAdNetworkDomainReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReportInterface as UnifiedNetworkSiteReportInterface;

interface AdNetworkDomainReportInterface extends ComparisonReportInterface
{
    /**
     * @return string
     */
    public function getDomain();

    /**
     * @param string $domain
     * @return self
     */
    public function setDomain($domain);

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
     * @return PerformanceAdNetworkDomainReportInterface
     */
    public function getPerformanceAdNetworkDomainReport();

    /**
     * @param PerformanceAdNetworkDomainReportInterface $performanceAdNetworkDomainReport
     * @return self
     */
    public function setPerformanceAdNetworkDomainReport($performanceAdNetworkDomainReport);

    /**
     * @return UnifiedNetworkSiteReportInterface
     */
    public function getUnifiedNetworkSiteReport();

    /**
     * @param UnifiedNetworkSiteReportInterface $unifiedNetworkSiteReport
     * @return self
     */
    public function setUnifiedNetworkSiteReport($unifiedNetworkSiteReport);
}