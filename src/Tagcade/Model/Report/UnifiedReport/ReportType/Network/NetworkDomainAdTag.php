<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Network;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagReportInterface;

class NetworkDomainAdTag extends AbstractReportType
{
    const REPORT_TYPE = 'unified.network.adtag';

    /** @var AdNetworkInterface */
    private $adNetwork;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $partnerTagId;

    /**
     * Account constructor.
     * @param AdNetworkInterface $adNetwork
     * @param string $domain
     * @param string $partnerTagId
     */
    public function __construct($adNetwork, $domain, $partnerTagId)
    {
        if ($adNetwork instanceof AdNetworkInterface) {
            $this->adNetwork = $adNetwork;
        }

        $this->domain = $domain;
        $this->partnerTagId = $partnerTagId;
    }

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof NetworkDomainAdTagReportInterface;
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
     * @return null
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }
}