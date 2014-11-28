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
            $this->getTotalBilledAmount(),
            $this->getEstCpm(),
            $this->getEstRevenue(),

            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue()

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
    public function getTotalBilledAmount()
    {
        return $this->billedAmount;
    }


}