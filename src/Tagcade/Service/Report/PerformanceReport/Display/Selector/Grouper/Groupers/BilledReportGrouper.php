<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

class BilledReportGrouper extends AbstractGrouper
{
    private $slotOpportunities;
    private $billedAmount;

    private $averageSlotOpportunities;

    public function getGroupedReport()
    {
        return new BilledReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getReportName(),
            $this->getTotalOpportunities(),
            $this->getSlotOpportunities(), // added field
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getBilledAmount(),
            $this->getEstCpm(),
            $this->getEstRevenue(),

            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getAverageSlotOpportunities()
        );
    }

    protected  function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());
        $this->averageSlotOpportunities = $this->getRatio($this->getSlotOpportunities(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        if (!$report instanceof BilledReportDataInterface) {
            throw new InvalidArgumentException('Can only grouped BilledReportData instances');
        }

        parent::doGroupReport($report);

        $this->addSlotOpportunities($report->getSlotOpportunities());
        $this->addBilledAmount($report->getBilledAmount());
    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int) $slotOpportunities;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float) $billedAmount;
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @return float
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return int
     */
    public function getAverageSlotOpportunities()
    {
        return $this->averageSlotOpportunities;
    }
}