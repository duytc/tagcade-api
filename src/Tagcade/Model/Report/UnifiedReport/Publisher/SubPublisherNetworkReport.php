<?php


namespace Tagcade\Model\Report\UnifiedReport\Publisher;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherNetworkReport extends AbstractUnifiedReport implements SubPublisherNetworkReportInterface
{
    use PartnerReportFields;
    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

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

    protected function setDefaultName()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            $this->setName($this->adNetwork->getName());
        }
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

    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;

        return $this;
    }

    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;

        return $this;
    }
}