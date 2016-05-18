<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReportInterface as PerformanceAdNetworkDomainAdTagReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagReportInterface as UnifiedAdNetworkDomainAdTagReportInterface;

interface AdNetworkDomainAdTagReportInterface extends ComparisonReportInterface
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
     * @return PerformanceAdNetworkDomainAdTagReportInterface
     */
    public function getPerformanceAdNetworkDomainAdTagReport();

    /**
     * @param PerformanceAdNetworkDomainAdTagReportInterface $performanceAdNetworkDomainAdTagReport
     * @return self
     */
    public function setPerformanceAdNetworkDomainAdTagReport($performanceAdNetworkDomainAdTagReport);

    /**
     * @return UnifiedAdNetworkDomainAdTagReportInterface
     */
    public function getUnifiedAdNetworkDomainAdTagReport();

    /**
     * @param UnifiedAdNetworkDomainAdTagReportInterface $unifiedAdNetworkDomainAdTagReport
     * @return self
     */
    public function setUnifiedAdNetworkDomainAdTagReport($unifiedAdNetworkDomainAdTagReport);
}