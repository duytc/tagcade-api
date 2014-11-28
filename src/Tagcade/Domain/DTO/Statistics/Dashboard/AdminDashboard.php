<?php

namespace Tagcade\Domain\DTO\Statistics\Dashboard;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics;
use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\SiteStatistics;

class AdminDashboard
{

    /**
     * @var PlatformStatistics
     */
    protected $platformStatistics;

    /**
     * @var DaySummary
     */
    protected $todaySummary;

    /**
     * @var DaySummary
     */
    protected $yesterdaySummary;

    /**
     * @var AccountStatistics[]
     */
    protected $topPublishers;

    /**
     * @var CalculatedReportGroup[]
     */
    protected $topSites;

    /**
     * @var array
     */
    protected $reportDetails;


    function __construct(
        PlatformStatistics $platformStatistics,
        DaySummary $todaySummary = null,
        DaySummary $yesterdaySummary = null,
        array $topPublisher = null,
        array $topSites = null,
        array $reportDetails = null
    )
    {
        $this->platformStatistics = $platformStatistics;
        $this->todaySummary = $todaySummary;
        $this->yesterdaySummary = $yesterdaySummary;

        $this->topPublishers = $topPublisher;
        $this->topSites = $topSites;
        $this->reportDetails = $reportDetails;
    }

    /**
     * @return PlatformStatistics
     */
    public function getPlatformStatistics()
    {
        return $this->platformStatistics;
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
     * @return AccountStatistics[]
     */
    public function getTopPublishers()
    {
        return $this->topPublishers;
    }

    /**
     * @return \Tagcade\Domain\DTO\Statistics\SitesStatistics[]
     */
    public function getTopSites()
    {
        return $this->topSites;
    }

    /**
     * @return array
     */
    public function getReportDetails()
    {
        return $this->reportDetails;
    }


}