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
    private $rtbImpressions;

    private $averageSlotOpportunities;
    private $averageRtbImpressions;
    private $averageBilledAmount;

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
            $this->getAverageSlotOpportunities(),
            $this->getAverageBilledAmount(),
            $this->getRtbImpressions(),
            $this->getAverageRtbImpressions()
        );
    }

    protected  function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());

        $this->averageSlotOpportunities = $this->getRatio($this->getSlotOpportunities(), $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
        $this->averageRtbImpressions = $this->getRatio($this->getRtbImpressions(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        if (!$report instanceof BilledReportDataInterface) {
            throw new InvalidArgumentException('Can only grouped BilledReportData instances');
        }

        parent::doGroupReport($report);

        $this->addSlotOpportunities($report->getSlotOpportunities());
        $this->addBilledAmount($report->getBilledAmount());
        $this->addRtbImpressions($report->getRtbImpressions());
    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int) $slotOpportunities;
    }

    protected function addRtbImpressions($rtbImpressions)
    {
        $this->rtbImpressions += (int)$rtbImpressions;
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

    public function getRtbImpressions()
    {
        return $this->rtbImpressions;
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

    public function getAverageRtbImpressions()
    {
        return $this->averageRtbImpressions;
    }

    /**
     * @return float
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }


}