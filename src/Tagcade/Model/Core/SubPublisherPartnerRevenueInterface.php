<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherPartnerRevenueInterface extends ModelInterface
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
     * @return int
     */
    public function getSubPublisherId();

    /**
     * @return int
     */
    public function getRevenueOption();

    /**
     * @param int $revenueOption
     * @return self
     */
    public function setRevenueOption($revenueOption);

    /**
     * @return float
     */
    public function getRevenueValue();

    /**
     * @param float $revenueValue
     * @return self
     */
    public function setRevenueValue($revenueValue);

    /**
     * @return AdNetworkPartner
     */
    public function getAdNetworkPartner();

    /**
     * @param AdNetworkPartner $adNetworkPartner
     * @return self
     */
    public function setAdNetworkPartner(AdNetworkPartner $adNetworkPartner);
}