<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\AbstractGrouper;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;

class CalculatedReportGrouper extends AbstractGrouper
{
    private $slotOpportunities;
    private $billedAmount;

    private $averageSlotOpportunities;

    public function getGroupedReport()
    {
        return new CalculatedReportGroup(
            $this->getReportType(),
            $this->getReports(),
            $this->getReportName(),
            $this->getStartDate(),
            $this->getEndDate(),
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

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int) $slotOpportunities;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float) $billedAmount;
    }

    protected  function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());
        $this->averageSlotOpportunities = $this->getRatio($this->getSlotOpportunities(), $reportCount);
    }

    protected function doGroupReport(ReportInterface $report)
    {
        parent::doGroupReport($report);

        /** @var CalculatedReportInterface $report */

        $this->addSlotOpportunities($report->getSlotOpportunities());
        $this->addBilledAmount($report->getBilledAmount());
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @inheritdoc
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getAverageSlotOpportunities()
    {
        return $this->averageSlotOpportunities;
    }


}