<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint;

use Tagcade\Service\Report\UnifiedReport\Result\Group\UnifiedReportGroup;

class PulsePointRevenueReportGroup extends UnifiedReportGroup
{
    protected $revenue;
    protected $backupImpression;

    protected $avgCpm;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, array $reports, $name,
                                $paidImps, $totalImps, $revenue, $backupImpression, $avgCpm)
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name, $paidImps, $totalImps);

        $this->revenue = $revenue;
        $this->backupImpression = $backupImpression;
        $this->avgCpm = $avgCpm;
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