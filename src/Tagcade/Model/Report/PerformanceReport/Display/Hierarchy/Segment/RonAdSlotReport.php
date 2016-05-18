<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields\SlotOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the doCalculateFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the doCalculateFields method is custom for this type of report
 */
class RonAdSlotReport extends BaseAbstractCalculatedReport implements RonAdSlotReportInterface
{
    use SuperReportTrait;
    use SlotOpportunitiesTrait;

    /** @var RonAdSlotInterface */
    protected $ronAdSlot;

    /**
     * @var SegmentInterface
     */
    protected $segment;

    /** @var float */
    protected $customRate;

    /** @return RonAdSlotInterface */
    public function getRonAdSlot()
    {
        return $this->ronAdSlot;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot)
    {
        $this->ronAdSlot = $ronAdSlot;
        if (null === $this->getName() && $ronAdSlot instanceof RonAdSlotInterface) {
            $this->setName($ronAdSlot->getName());
        }

        return $this;
    }

    public function getRonAdSlotId()
    {
        if ($this->ronAdSlot instanceof RonAdSlotInterface) {
            return $this->ronAdSlot->getId();
        }

        return null;
    }

    /**
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
        return $this;
    }

    public function setThresholdBilledAmount($chainToSubReports = true)
    {
        // We don't need to calculated billed amount for ron slot since it is calculated for all slots already
    }


    /**
     * @return null|string
     */
    public function getSegmentName()
    {
        if ($this->segment instanceof SegmentInterface) {
            return $this->segment->getName();
        }

        return null;
    }

    protected function resetCounts()
    {
        if ($this->ronAdSlot->getLibraryAdSlot() instanceof LibraryDisplayAdSlotInterface) {
            parent::resetCounts();

            return;
        }

        if (!$this->ronAdSlot->getLibraryAdSlot() instanceof LibraryNativeAdSlotInterface) {
            return;
        }
        
        $this->totalOpportunities = 0;
        $this->impressions = 0;
        $this->passbacks = null;
        $this->estRevenue = null;
    }

    /**
     * @return float
     */
    public function getCustomRate()
    {
        return $this->customRate;
    }

    /**
     * @param float $customRate
     * @return $this
     */
    public function setCustomRate($customRate)
    {
        $this->customRate = $customRate;

        return $this;
    }


//    /**
//     * @param RonAdSlotInterface $adSlot
//     * @return $this
//     */
//    public function setAdSlot(RonAdSlotInterface $adSlot)
//    {
//        $this->ronAdSlot = $adSlot;
//        return $this;
//    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof RonAdTagReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SegmentReportInterface;
    }

    /**
     * Overwrite the parent doCalculateFields
     *
     * This is because the sub reports are not calculated and contain the base values
     *
     * @throws \Tagcade\Exception\RuntimeException
     */
    protected function doCalculateFields()
    {
        if ($this->slotOpportunities === null) {
            throw new RuntimeException('slotOpportunities must be set for an AdSlotReport, it is required to calculate the relative fill rate for an AdTagReport');
        }

        parent::doCalculateFields();
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof RonAdTagReportInterface) {
            throw new InvalidArgumentException('Expected RonAdTagReportInterface');
        }

        $subReport->setRelativeFillRate($this->getSlotOpportunities());

        if ($this->ronAdSlot->getLibraryAdSlot() instanceof ReportableLibraryAdSlotInterface) {
            parent::aggregateSubReport($subReport);
        }
        else {
            $this->addTotalOpportunities($subReport->getTotalOpportunities());
            $this->addImpressions($subReport->getImpressions());
        }
    }

    protected function setDefaultName()
    {
        if ($this->ronAdSlot->getLibraryAdSlot() instanceof ReportableLibraryAdSlotInterface) {
            $this->setName($this->ronAdSlot->getName());
        }
    }
}