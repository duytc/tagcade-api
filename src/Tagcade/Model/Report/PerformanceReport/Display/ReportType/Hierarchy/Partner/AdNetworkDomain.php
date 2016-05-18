<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;

class AdNetworkDomain extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'partner.adNetworkDomain';

    /** @var string the domain of site */
    private $domain;

    /** @var AdNetworkInterface */
    private $adNetwork;

    public function __construct(AdNetworkInterface $adNetwork = null, $domain = null)
    {
        $this->adNetwork = $adNetwork instanceof AdNetworkInterface ? $adNetwork : null;
        $this->domain = $domain;
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
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkDomainReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }
}