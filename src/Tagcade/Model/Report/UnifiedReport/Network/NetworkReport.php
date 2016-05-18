<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;



use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\AbstractUnifiedReport;
class NetworkReport extends AbstractUnifiedReport implements NetworkReportInterface
{
    const ALL_AD_NETWORK = 'All Demand Partners';
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;


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
        if ($this->adNetwork instanceof AdNetworkInterface) {
            $this->setName($this->adNetwork->getName());
        }
    }


    public function getName()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getName();
        }

        return self::ALL_AD_NETWORK;
    }
}