<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\AbstractGrouper;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;

class CalculatedReportGrouper extends AbstractGrouper
{
    private $slotOpportunities;

    public function getGroupedReport()
    {
        return new CalculatedReportGroup(
            $this->getReportType(),
            $this->getReportName(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getTotalOpportunities(),
            $this->getSlotOpportunities(), // added field
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate()
        );
    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int) $slotOpportunities;
    }

    protected function doGroupReport(ReportInterface $report)
    {
        parent::doGroupReport($report);

        /** @var CalculatedReportInterface $report */

        $this->addSlotOpportunities($report->getSlotOpportunities());
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
}