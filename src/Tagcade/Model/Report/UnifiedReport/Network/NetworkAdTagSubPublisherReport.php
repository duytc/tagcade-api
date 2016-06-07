<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class NetworkAdTagSubPublisherReport extends AbstractUnifiedReport implements NetworkAdTagSubPublisherReportInterface
{
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $partnerTagId;

    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
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

    public function getName()
    {
        return $this->partnerTagId;
    }
}