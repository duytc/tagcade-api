<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;

class NetworkDomainAdTagReport extends AbstractUnifiedReport implements NetworkDomainAdTagReportInterface
{
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $partnerTagId;

    protected $domain;

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @return int|null
     */
    public function getAdNetworkId()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getId();
        }

        return null;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;

        return $this;
    }

    protected function setDefaultName()
    {
        $this->setName($this->getPartnerTagId());
    }

    /**
     * @return mixed
     */
    public function getPartnerTagId()
    {
        return strval($this->partnerTagId);
    }

    /**
     * @param $partnerTagId
     * @return $this
     */
    public function setPartnerTagId($partnerTagId)
    {
        $this->partnerTagId = $partnerTagId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }


    public function getName()
    {
        return $this->partnerTagId;
    }
}