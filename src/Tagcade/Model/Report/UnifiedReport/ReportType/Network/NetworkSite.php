<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReport;
use Tagcade\Model\User\Role\PublisherInterface;

class NetworkSite extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.site';
    protected $domain;
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
     * @param AdNetworkInterface|null $adNetwork
     * @param PublisherInterface $publisher
     * @param $domain
     */
    public function __construct(PublisherInterface $publisher, $adNetwork, $domain)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        $this->publisher = $publisher;
        $this->domain = $domain;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkSiteReport;
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

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
}