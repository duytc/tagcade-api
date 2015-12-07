<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
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

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, SlidingPagination $pagination, $name,
                                $paidImps, $totalImps, $fillRate,
                                $averageFillRate, $averagePaidImps, $averageTotalImps,
                                $revenue, $backupImpression, $avgCpm,
                                $averageAvgCpm, $averageRevenue, $averageBackupImpression)
    {
        parent::__construct($reportType, $startDate, $endDate, $pagination, $name, $paidImps, $totalImps, $fillRate,
            $averageFillRate, $averagePaidImps, $averageTotalImps);

        // total report
        $this->revenue = $revenue;
        $this->backupImpression = $backupImpression;

        // average report
        $this->averageAvgCpm = round($averageAvgCpm, 4);
        $this->averageRevenue = round($averageRevenue, 4);
        $this->averageBackupImpression = round($averageBackupImpression, 4);

        // weighted report
        $this->avgCpm = round($avgCpm, 4);
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
}