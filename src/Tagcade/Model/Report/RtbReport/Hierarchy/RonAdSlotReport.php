<?php


namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\RtbReport\AbstractReport;

class RonAdSlotReport extends AbstractReport implements RonAdSlotReportInterface
{
    /**
     * @var RonAdSlotInterface
     */
    protected $ronAdSlot;

    /**
     * @var SegmentInterface
     */
    protected $segment;

    /**
     * @return RonAdSlotInterface|null
     */
    public function getRonAdSlot()
    {
        return $this->ronAdSlot;
    }

    /**
     * @return int|null
     */
    public function getRonAdSlotId()
    {
        if ($this->ronAdSlot instanceof RonAdSlotInterface) {
            return $this->ronAdSlot->getId();
        }

        return null;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return $this
     */
    public function setRonAdSlot(RonAdSlotInterface $ronAdSlot)
    {
        $this->ronAdSlot = $ronAdSlot;
        return $this;
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

    /**
     * @return null
     */
    public function getSegmentId()
    {
        if ($this->segment instanceof SegmentInterface) {
            return $this->segment->getId();
        }

        return null;
    }


    protected function setDefaultName()
    {
        if ($this->ronAdSlot instanceof RonAdSlotInterface) {
            $this->setName($this->ronAdSlot->getName());
        }
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getOpportunities() === null) {
            throw new RuntimeException('total opportunities must be defined to calculate ad tag fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports
        return $this->getPercentage($this->getImpressions(), $this->getOpportunities());
    }
}