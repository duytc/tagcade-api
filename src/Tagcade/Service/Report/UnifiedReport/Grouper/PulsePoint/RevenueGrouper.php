<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint;


use InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportModelInterface;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportRevenueInterface;
use Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint\PulsePointRevenueReportGroup;

class RevenueGrouper extends DefaultGrouper
{
    protected $revenue;
    protected $backupImpression;

    protected $avgCpm;

    public function getGroupedReport()
    {
        return new PulsePointRevenueReportGroup(
            $this->reportType,
            $this->startDate,
            $this->endDate,
            $this->reports,
            $this->reportName,
            $this->paidImps,
            $this->totalImps,
            $this->revenue,
            $this->backupImpression,
            $this->avgCpm
        );
    }

    protected function doGroupReport(PulsePointUnifiedReportModelInterface $report)
    {
        if (!$report instanceof PulsePointUnifiedReportRevenueInterface) {
            throw new InvalidArgumentException('Can only grouped PulsePointUnifiedReportRevenueInterface instances');
        }

        parent::doGroupReport($report);

        $this->addRevenue($report->getRevenue());
        $this->addBackupImpression($report->getBackupImpression());
    }

    protected function addRevenue($revenue)
    {
        $this->revenue += (float)$revenue;
    }

    protected function addBackupImpression($backupImpression)
    {
        $this->backupImpression += (float)$backupImpression;
    }
} 