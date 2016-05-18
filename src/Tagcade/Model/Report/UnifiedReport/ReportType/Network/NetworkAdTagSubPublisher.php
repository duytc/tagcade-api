<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport;

class NetworkAdTagSubPublisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.adtag.subpublisher';
    protected $partnerTagId;
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;
    protected  $subPublisherId;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param $partnerTagId
     * @param $subPublisherId
     */
    public function __construct($adNetwork, $partnerTagId, $subPublisherId)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        $this->partnerTagId = $partnerTagId;
        $this->subPublisherId = $subPublisherId;
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

    public function getSubPublisherId()
    {
        return $this->subPublisherId;

    }
}