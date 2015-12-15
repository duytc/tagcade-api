<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue;
use Tagcade\Service\Report\UnifiedReport\Result\Group\UnifiedReportGroup;

class PulsePointRevenueReportGroup extends UnifiedReportGroup
{
    // as total value
    protected $revenue;
    protected $backupImpression;

    // as weighted value
    protected $avgCpm;

    // as average value
    protected $averageAvgCpm;
    protected $averageRevenue;
    protected $averageBackupImpression;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, $reports, $totalRecord, $name, AverageValue $avg)
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $totalRecord, $name, $avg);

        // total report
        $this->revenue = floatval($avg->getRevenue());
        $this->pubPayout = floatval($avg->getPubPayout());
        $this->averagePubPayout = floatval($avg->getAveragePubPayout());
        $this->backupImpression = intval($avg->getBackupImpression());

        // average report
        $this->averageAvgCpm = round($avg->getAverageAvgCpm(), 4);
        $this->averageRevenue = round($avg->getAverageRevenue(), 4);
        $this->averageBackupImpression = round($avg->getAverageBackupImpression(), 4);

        // weighted report
        $this->avgCpm = round($avg->getAvgCpm(), 4);
    }

    /**
     * @return mixed
     */
    public function getAvgCpm()
    {
        return $this->avgCpm;
    }

    /**
     * @return mixed
     */
    public function getBackupImpression()
    {
        return $this->backupImpression;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @return float
     */
    public function getAverageAvgCpm()
    {
        return $this->averageAvgCpm;
    }

    /**
     * @return float
     */
    public function getAverageBackupImpression()
    {
        return $this->averageBackupImpression;
    }

    /**
     * @return float
     */
    public function getAverageRevenue()
    {
        return $this->averageRevenue;
    }
}