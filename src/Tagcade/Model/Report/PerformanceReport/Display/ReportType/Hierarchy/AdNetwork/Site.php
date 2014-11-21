<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class Site extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'adNetwork.site';

    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * @var AdNetworkInterface
     */
    private $adNetwork;

    public function __construct(SiteInterface $site, AdNetworkInterface $adNetwork)
    {
        $this->site = $site;
        $this->adNetwork = $adNetwork;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @return int|null
     */
    public function getSiteId()
    {
        return $this->site->getId();
    }

    /**
     * @return int|null
     */
    public function getAdNetworkId()
    {
        return $this->adNetwork->getId();
    }

    /**
     * @inheritdoc
     */
    public function isValidReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }
}