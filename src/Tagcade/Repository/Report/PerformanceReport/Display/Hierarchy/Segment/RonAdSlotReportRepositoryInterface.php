<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;

interface RonAdSlotReportRepositoryInterface
{
    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportForRonAdSlot(RonAdSlotInterface $ronAdSlot, DateTime $startDate, DateTime $endDate);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param SegmentInterface $segment
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportForRonSegment(RonAdSlotInterface $ronAdSlot, SegmentInterface $segment, DateTime $startDate, DateTime $endDate);
}