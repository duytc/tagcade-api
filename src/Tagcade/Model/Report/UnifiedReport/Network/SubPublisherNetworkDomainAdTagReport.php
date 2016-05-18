<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherNetworkDomainAdTagReport extends AbstractUnifiedReport implements SubPublisherNetworkDomainAdTagReportInterface
{
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $partnerTagId;

    protected $domain;

    protected $subPublisher;

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

    /**
     * @return mixed
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;
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
        return $this->partnerTagId;
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