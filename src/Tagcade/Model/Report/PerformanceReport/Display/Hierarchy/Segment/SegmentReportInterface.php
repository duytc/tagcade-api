<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment;

use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\BillableInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;

interface SegmentReportInterface extends BillableInterface, CalculatedReportInterface, SubReportInterface
{
    /**
     * @return SegmentInterface
     */
    public function getSegment();

    /**
     * @return int|null
     */
    public function getSegmentId();

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment(SegmentInterface $segment);
}
