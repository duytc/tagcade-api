<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class NetworkDomainAdTagSubPublisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.adtag.subpublisher';

    /** @var AdNetworkInterface */
    private $adNetwork;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $partnerTagId;
    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    /**
     * NetworkDomainAdTagSubPublisher constructor.
     * @param $subPublisher
     * @param $adNetwork
     * @param $domain
     * @param $partnerTagId
     */
    public function __construct($subPublisher, $adNetwork, $domain, $partnerTagId)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        if ($subPublisher instanceof SubPublisherInterface) {
            $this->subPublisher = $subPublisher;
        }

        $this->domain = $domain;
        $this->partnerTagId = $partnerTagId;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkDomainAdTagSubPublisherReportInterface;
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
     * @return null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return null
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }
}