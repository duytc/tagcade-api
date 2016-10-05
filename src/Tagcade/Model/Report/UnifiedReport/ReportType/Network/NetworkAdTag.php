<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagReport;
use Tagcade\Model\User\Role\PublisherInterface;

class NetworkAdTag extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.adtag';
    protected $partnerTagId;
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * Account constructor.
     * @param PublisherInterface $publisher
     * @param AdNetworkInterface $adNetwork
     * @param $partnerTagId
     */
    public function __construct(PublisherInterface $publisher, $adNetwork, $partnerTagId)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        $this->publisher = $publisher;
        $this->partnerTagId = $partnerTagId;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkAdTagReport;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

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
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
}