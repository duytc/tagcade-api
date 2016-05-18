<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;

interface AdNetworkDomainAdTagReportInterface extends CalculatedReportInterface, RootReportInterface, PartnerReportInterface
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
     * @return mixed
     */
    public function getPartnerTagId();

    /**
     * @param mixed $partnerTagId
     * @return self
     */
    public function setPartnerTagId($partnerTagId);
}
