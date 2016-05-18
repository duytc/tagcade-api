<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields\SlotOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

/**
 * A calculated report in the platform Reports contains sub reports
 *
 * i.e an ad slot report contains many ad tag reports
 *
 * These sub reports are used to generated the values for this report
 */
abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements CalculatedReportInterface, SuperReportInterface
{
    use SlotOpportunitiesTrait;

    protected function doCalculateFields()
    {
        $this->slotOpportunities = 0;
        $this->billedAmount = 0;
        $this->rtbImpressions = 0;

        parent::doCalculateFields();

    }

    public function setThresholdBilledAmount($chainToSubReports = true)
    {
        $this->billedAmount = 0;
        foreach ($this->subReports as $subReport) {
            if ($chainToSubReports === true && $subReport instanceof AbstractCalculatedReport) {
                $subReport->setThresholdBilledAmount(); // chain the calls to setCalculatedFields
            }

            $this->addBilledAmount($subReport->getBilledAmount());

            unset($subReport);
        }

        $this->setWeightedBilledRate();
    }

    protected function postCalculateFields()
    {
        $this->setWeightedBilledRate();
    }

    protected function setWeightedBilledRate()
    {
        $weightedCpmRate = $this->calculateWeightedValue($this->getSubReports(), 'billedRate', 'billedAmount');
        $this->setBilledRate($weightedCpmRate);
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expected a CalculatedReportInterface');
        }

        $this->addSlotOpportunities($subReport->getSlotOpportunities());
        $this->addRtbImpressions($subReport->getRtbImpressions());
        $this->addBilledAmount($subReport->getBilledAmount());

        parent::aggregateSubReport($subReport);

    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += $slotOpportunities;
    }

    protected function addRtbImpressions($rtbImpressions)
    {
        $this->rtbImpressions += $rtbImpressions;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float)$billedAmount;
    }
}