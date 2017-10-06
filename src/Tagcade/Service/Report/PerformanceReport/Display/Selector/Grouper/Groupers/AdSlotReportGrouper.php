<?php
namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\AdSlotReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\AdSlotReportGroup;

class AdSlotReportGrouper extends BilledReportGrouper
{
    private $refreshedSlotOpportunities;

    private $averageRefreshedSlotOpportunities;

    public function getGroupedReport()
    {
        return new AdSlotReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getReportName(),
            $this->getTotalOpportunities(),
            $this->getSlotOpportunities(), // added field
            $this->getRefreshedSlotOpportunities(), // for ad slot only
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getBilledAmount(),
            $this->getEstCpm(),
            $this->getEstRevenue(),
            $this->getAdOpportunities(),
            $this->getOpportunityFillRate(),

            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getAverageSlotOpportunities(),
            $this->getAverageRefreshedSlotOpportunities(),
            $this->getAverageBilledAmount(),

            $this->getInBannerRequests(),
            $this->getInBannerTimeouts(),
            $this->getInBannerBilledAmount(),
            $this->getInBannerImpressions(),

            $this->getAverageInBannerRequests(),
            $this->getAverageInBannerTimeouts(),
            $this->getAverageInBannerBilledAmount(),
            $this->getAverageInBannerImpressions(),
            $this->getAverageAdOpportunities(),
            $this->getAverageOpportunityFillRate()
        );
    }

    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());

        $this->averageRefreshedSlotOpportunities = $this->getRatio($this->getRefreshedSlotOpportunities(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        if (!$report instanceof AdSlotReportDataInterface) {
            throw new InvalidArgumentException('Can only grouped AdSlotReportData instances');
        }

        parent::doGroupReport($report);

        $this->addRefreshedSlotOpportunities($report->getRefreshedSlotOpportunities());
    }

    protected function addRefreshedSlotOpportunities($refreshedSlotOpportunities)
    {
        $this->refreshedSlotOpportunities += (int)$refreshedSlotOpportunities;
    }

    /**
     * @return int
     */
    public function getRefreshedSlotOpportunities()
    {
        return $this->refreshedSlotOpportunities;
    }

    /**
     * @return int
     */
    public function getAverageRefreshedSlotOpportunities()
    {
        return $this->averageRefreshedSlotOpportunities;
    }
}