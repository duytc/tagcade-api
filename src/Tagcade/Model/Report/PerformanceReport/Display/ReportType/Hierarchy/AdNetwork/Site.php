<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class Site implements ReportTypeInterface
{
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
}