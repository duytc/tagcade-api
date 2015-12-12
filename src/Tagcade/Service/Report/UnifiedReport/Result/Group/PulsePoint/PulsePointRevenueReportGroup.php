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

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, SlidingPagination $pagination, $name, AverageValue $avg)
    {
        parent::__construct($reportType, $startDate, $endDate, $pagination, $name, $avg);

        // total report
        $this->revenue = floatval($avg->getRevenue());
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
}