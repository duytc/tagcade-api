<?php

namespace Tagcade\Domain\DTO\Statistics\Dashboard;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics;
use Tagcade\Domain\DTO\Statistics\Hierarchy\AdNetwork\AdNetworkStatistics;
use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\SiteStatistics;

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
     * @var CalculatedReportGroup[]
     */
    protected $topSite;

    /**
     * @var ReportGroup[]
     */
    protected $topAdNetworks;

    /**
     * @var array
     */
    protected $reportDetails;

    function __construct(
        AccountStatistics $accountStatistics,
        DaySummary $todaySummary = null,
        DaySummary $yesterdaySummary = null,
        array $topSite = null,
        array $topAdNetworks = null,
        array $reportDetails = null
    )
    {
        $this->accountStatistics = $accountStatistics;
        $this->todaySummary = $todaySummary;
        $this->yesterdaySummary = $todaySummary;

        $this->topSite = $topSite;
        $this->topAdNetworks = $topAdNetworks;

        $this->reportDetails = $reportDetails;
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
     * @return CalculatedReportGroup[]
     */
    public function getTopSite()
    {
        return $this->topSite;
    }

    /**
     * @return ReportGroup[]
     */
    public function getTopAdNetworks()
    {
        return $this->topAdNetworks;
    }

    /**
     * @return array
     */
    public function getReportDetails()
    {
        return $this->reportDetails;
    }


}