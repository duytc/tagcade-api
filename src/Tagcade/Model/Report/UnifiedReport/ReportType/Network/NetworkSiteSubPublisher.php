<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteSubPublisherReport;
use Tagcade\Model\User\Role\SubPublisherInterface;

class NetworkSiteSubPublisher extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.site.subpublisher';
    protected $domain;
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /**
     * @var SubPublisherInterface
     */
    private $subPublisher;
    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     * @param $subPublisher
     */
    public function __construct($adNetwork, $domain, $subPublisher)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        if ($subPublisher instanceof SubPublisherInterface) {
            $this->subPublisher = $subPublisher;
        }

        $this->domain = $domain;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkSiteSubPublisherReport;
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
    public function getDomain()
    {
        return $this->domain;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    public function getSubPublisher()
    {
        return $this->subPublisher;
    }
}