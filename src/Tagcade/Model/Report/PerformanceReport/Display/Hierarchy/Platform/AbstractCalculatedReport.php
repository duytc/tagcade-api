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

        $this->inBannerTimeouts = 0;
        $this->inBannerImpressions = 0;
        $this->inBannerBilledAmount = 0;
        $this->inBannerRequests = 0;

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

        $inBannerWeightedCpmRate = $this->calculateWeightedValue($this->getSubReports(), 'inBannerBilledRate', 'inBannerBilledAmount');
        $this->setInBannerBilledRate($inBannerWeightedCpmRate);
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expected a CalculatedReportInterface');
        }

        $this->addSlotOpportunities($subReport->getSlotOpportunities());
        $this->addBilledAmount($subReport->getBilledAmount());

        $this->addInBannerBilledAmount($subReport->getInBannerBilledAmount());
        $this->addInBannerTimeouts($subReport->getInBannerTimeouts());
        $this->addInBannerRequests($subReport->getInBannerRequests());
        $this->addInBannerImpressions($subReport->getInBannerImpressions());

        parent::aggregateSubReport($subReport);

    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += $slotOpportunities;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float)$billedAmount;
    }

    protected function addInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests += (int)$inBannerRequests;
    }

    protected function addInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions += (int)$inBannerImpressions;
    }

    protected function addInBannerBilledAmount($inBannerBilledAmount)
    {
        $this->inBannerBilledAmount += (float)$inBannerBilledAmount;
    }

    protected function addInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts += (int)$inBannerTimeouts;
    }
}