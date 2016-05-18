<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkReport;

class Network extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network';
    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     */
    public function __construct(AdNetworkInterface $adNetwork)
    {
        $this->adNetwork = $adNetwork;
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

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkReport;
    }
}