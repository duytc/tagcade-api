<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class NetworkAdTagSubPublisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.adtag.subpublisher';
    protected $partnerTagId;
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param $partnerTagId
     * @param $subPublisher
     */
    public function __construct($adNetwork, $partnerTagId, $subPublisher)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        $this->partnerTagId = $partnerTagId;
        if ($subPublisher instanceof SubPublisherInterface) {
            $this->subPublisher = $subPublisher;
        }
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkAdTagSubPublisherReport;
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
}