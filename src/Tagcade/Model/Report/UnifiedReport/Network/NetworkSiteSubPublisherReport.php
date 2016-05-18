<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class NetworkSiteSubPublisherReport extends AbstractUnifiedReport implements NetworkSiteSubPublisherReportInterface
{
    /** @var AdNetworkInterface */
    protected $adNetwork;

    protected $domain;

    /** @var SubPublisherInterface */
    protected $subPublisher;

    /** @return SubPublisherInterface */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     * @return $this
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;

        return $this;
    }

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
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
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
        $this->setName($this->getDomain());
    }

    public function getName()
    {
        return $this->domain;
    }
}