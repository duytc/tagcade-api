<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

abstract class AbstractReportType implements ReportTypeInterface, ReportTypePartnerInterface
{
    const REPORT_TYPE = null;

    /**
     * @var PublisherInterface
     */
    protected $publisher;
    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    /**
     * AbstractReportType constructor.
     * @param AdNetworkInterface $adNetwork
     * @param $publisher
     * @param $subPublisher
     */
    public function __construct($adNetwork, $publisher = null, $subPublisher = null)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        if ($subPublisher instanceof SubPublisherInterface ) {
            $this->subPublisher = $subPublisher;
        }

        if ($publisher instanceof PublisherInterface) {
            $this->publisher = $publisher;
        }
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @return int|null
     */
    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @return null
     */
    public function getAdNetworkId()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getId();
        }

        return null;
    }

    /**
     * @return null
     */
    public function getReportType()
    {
        return static::REPORT_TYPE;
    }
}