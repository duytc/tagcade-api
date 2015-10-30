<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment;

use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\BillableInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;

interface RonAdSlotReportInterface extends BillableInterface, CalculatedReportInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return float
     */
    public function getCustomRate();

    /**
     * @param float $customRate
     */
    public function setCustomRate($customRate);

    /** @return RonAdSlotInterface */
    public function getRonAdSlot();

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot);

    /**
     * @return int
     */
    public function getRonAdSlotId();

    /**
     * @return SegmentInterface
     */
    public function getSegment();

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment($segment);

    /**
     * @return null|string
     */
    public function getSegmentName();
}