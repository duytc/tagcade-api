<?php

namespace Tagcade\Domain\DTO\Statistics\Dashboard;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics;
use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics;

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
     * @var BilledReportGroup[]
     */
    protected $topPublishers;

    /**
     * @var BilledReportGroup[]
     */
    protected $topSites;

    function __construct(
        PlatformStatistics $platformStatistics = null,
        DaySummary $todaySummary = null,
        DaySummary $yesterdaySummary = null,
        array $topPublishers = null,
        array $topSites = null
    )
    {
        $this->platformStatistics = $platformStatistics;
        $this->todaySummary = $todaySummary;
        $this->yesterdaySummary = $yesterdaySummary;

        $this->topPublishers = $topPublishers;
        $this->topSites = $topSites;
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
     * @return BilledReportGroup[]
     */
    public function getTopSites()
    {
        return $this->topSites;
    }
}