<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportInterface as PerformanceAdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportInterface as UnifiedAdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdNetworkDomainAdTagSubPublisherReportInterface extends ComparisonReportInterface
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
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @return mixed
     */
    public function getSubPublisherId();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher);

    /**
     * @return PerformanceAdNetworkDomainAdTagSubPublisherReportInterface
     */
    public function getPerformanceAdNetworkDomainAdTagSubPublisherReport();

    /**
     * @param PerformanceAdNetworkDomainAdTagSubPublisherReportInterface $performanceAdNetworkDomainAdTagSubPublisherReport
     * @return self
     */
    public function setPerformanceAdNetworkDomainAdTagSubPublisherReport($performanceAdNetworkDomainAdTagSubPublisherReport);

    /**
     * @return UnifiedAdNetworkDomainAdTagSubPublisherReportInterface
     */
    public function getUnifiedAdNetworkDomainAdTagSubPublisherReport();

    /**
     * @param UnifiedAdNetworkDomainAdTagSubPublisherReportInterface $unifiedAdNetworkDomainAdTagSubPublisherReport
     * @return self
     */
    public function setUnifiedAdNetworkDomainAdTagSubPublisherReport($unifiedAdNetworkDomainAdTagSubPublisherReport);
}