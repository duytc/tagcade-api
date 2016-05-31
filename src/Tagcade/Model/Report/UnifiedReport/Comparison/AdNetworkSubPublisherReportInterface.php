<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface as PerformanceSubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherNetworkReportInterface as UnifiedSubPublisherAdNetworkReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdNetworkSubPublisherReportInterface extends ComparisonReportInterface
{
    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher);

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
     * @return PerformanceSubPublisherAdNetworkReportInterface
     */
    public function getPerformanceSubPublisherAdNetworkReport();

    /**
     * @param PerformanceSubPublisherAdNetworkReportInterface $performanceSubPublisherAdNetworkReport
     * @return self
     */
    public function setPerformanceSubPublisherAdNetworkReport($performanceSubPublisherAdNetworkReport);

    /**
     * @return UnifiedSubPublisherAdNetworkReportInterface
     */
    public function getUnifiedSubPublisherAdNetworkReport();

    /**
     * @param UnifiedSubPublisherAdNetworkReportInterface $unifiedSubPublisherAdNetworkReport
     * @return self
     */
    public function setUnifiedSubPublisherAdNetworkReport($unifiedSubPublisherAdNetworkReport);
}