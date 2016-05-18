<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherNetworkDomainAdTagReportInterface {

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork();

    /**
     * @return int|null
     */
    public function getAdNetworkId();


    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function setAdNetwork($adNetwork);

    /**
     * @return mixed
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher);

    /**
     * @return mixed
     */
    public function getPartnerTagId();

    /**
     * @param $partnerTagId
     * @return $this
     */
    public function setPartnerTagId($partnerTagId);

    /**
     * @return mixed
     */
    public function getDomain();

    /**
     * @param mixed $domain
     * @return self
     */
    public function setDomain($domain);

    /**
     * @return mixed
     */
    public function getName();

} 