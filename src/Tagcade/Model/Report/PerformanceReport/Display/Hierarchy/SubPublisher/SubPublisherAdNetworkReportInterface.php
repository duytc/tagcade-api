<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherAdNetworkReportInterface extends CalculatedReportInterface, RootReportInterface, PartnerReportInterface
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
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher);
}