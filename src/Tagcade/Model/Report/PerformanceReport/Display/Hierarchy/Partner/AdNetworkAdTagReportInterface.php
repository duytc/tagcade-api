<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;

interface AdNetworkAdTagReportInterface extends CalculatedReportInterface, RootReportInterface, PartnerReportInterface
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
}
