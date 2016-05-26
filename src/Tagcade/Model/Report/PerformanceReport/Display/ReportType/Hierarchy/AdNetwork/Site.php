<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportInterface;
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

    /** @var bool [optional] temporarily field. If true, the result will return name using ad network name */
    private $_groupByAdNetwork = false;

    public function __construct(SiteInterface $site, AdNetworkInterface $adNetwork = null, $groupByAdNetwork = false)
    {
        $this->site = $site;
        $this->adNetwork = $adNetwork;
        $this->_groupByAdNetwork = boolval($groupByAdNetwork);
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
     * @return null|string
     */
    public function getSiteName()
    {
        return $this->site->getName();
    }

    /**
     * @return null|string
     */
    public function getAdNetworkName()
    {
        return $this->adNetwork->getName();
    }

    /**
     * @return boolean
     */
    public function isGroupByAdNetwork()
    {
        return $this->_groupByAdNetwork;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }
}