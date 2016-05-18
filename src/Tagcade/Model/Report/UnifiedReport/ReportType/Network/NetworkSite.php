<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReport;

class NetworkSite extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.site';
    protected $domain;
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param $domain
     */
    public function __construct($adNetwork, $domain)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

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
}