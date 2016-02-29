<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;

interface RonAdSlotReportInterface
{
    /**
     * @return RonAdSlotInterface
     */
    public function getRonAdSlot();

    /**
     * @return SegmentInterface
     */
    public function getSegment();
}