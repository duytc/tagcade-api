<?php

namespace Tagcade\Domain\DTO\Statistics\Dashboard;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics;
use Tagcade\Domain\DTO\Statistics\DaySummary;

class PublisherDashboard
{
    /**
     * @var AccountStatistics
     */
    public $accountStatistics;


    /**
     * @var DaySummary
     */
    protected $todaySummary;

    /**
     * @var DaySummary
     */
    protected $yesterdaySummary;

    /**
     * @var BilledReportGroup[]
     */
    protected $topSites;

    /**
     * @var ReportGroup[]
     */
    protected $topAdNetworks;

    function __construct(
        AccountStatistics $accountStatistics,
        DaySummary $todaySummary = null,
        DaySummary $yesterdaySummary = null,
        array $topSites = null,
        array $topAdNetworks = null
    )
    {
        $this->accountStatistics = $accountStatistics;
        $this->todaySummary = $todaySummary;
        $this->yesterdaySummary = $todaySummary;

        $this->topSites = $topSites;
        $this->topAdNetworks = $topAdNetworks;
    }

    /**
     * @return AccountStatistics
     */
    public function getAccountStatistics()
    {
        return $this->accountStatistics;
    }

    /**
     * @return DaySummary
     */
    public function getTodaySummary()
    {
        return $this->todaySummary;
    }

    /**
     * @return DaySummary
     */
    public function getYesterdaySummary()
    {
        return $this->yesterdaySummary;
    }

    /**
     * @return BilledReportGroup[]
     */
    public function getTopSite()
    {
        return $this->topSites;
    }

    /**
     * @return ReportGroup[]
     */
    public function getTopAdNetworks()
    {
        return $this->topAdNetworks;
    }
}