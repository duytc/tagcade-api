<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint;


use InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportModelInterface;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportRevenueInterface;
use Tagcade\Service\Report\UnifiedReport\Result\Group\PulsePoint\PulsePointRevenueReportGroup;

class RevenueGrouper extends DefaultGrouper
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

    public function getGroupedReport()
    {
        return new PulsePointRevenueReportGroup(
            $this->reportType,
            $this->startDate,
            $this->endDate,
            $this->pagination,
            $this->reportName,

            $this->paidImps,
            $this->totalImps,
            $this->fillRate,

            $this->averageFillRate,
            $this->averagePaidImps,
            $this->averageTotalImps,

            $this->revenue,
            $this->backupImpression,
            $this->avgCpm,

            $this->averageAvgCpm,
            $this->averageRevenue,
            $this->averageBackupImpression
        );
    }

    /**
     * @return mixed
     */
    public function getAverageAvgCpm()
    {
        return $this->averageAvgCpm;
    }

    /**
     * @param PulsePointUnifiedReportRevenueInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $totalAvgCpm = 0;

        // do the total AvgCpm
        foreach ($reports as $report) {
            $totalAvgCpm += $report->getAvgCpm();
        }

        // Calculate average
        $reportCount = count($this->getReports());
        $this->averageAvgCpm = $this->getRatio($totalAvgCpm, $reportCount);
        $this->averageRevenue = $this->getRatio($this->revenue, $reportCount);
        $this->averageBackupImpression = $this->getRatio($this->backupImpression, $reportCount);

        // Calculate weighted value for avgCpm
        // TODO make sure avgCpm using weighted value is correct
        $this->avgCpm = $this->calculateWeightedValue($reports, 'avgCpm', 'revenue');
    }

    protected function doGroupReport(PulsePointUnifiedReportModelInterface $report)
    {
        if (!$report instanceof PulsePointUnifiedReportRevenueInterface) {
            throw new InvalidArgumentException('Can only grouped PulsePointUnifiedReportRevenueInterface instances');
        }

        parent::doGroupReport($report);

        // for calculating total
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