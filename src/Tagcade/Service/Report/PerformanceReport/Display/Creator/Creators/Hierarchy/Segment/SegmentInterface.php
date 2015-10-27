<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\SegmentReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\Segment as SegmentReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;

interface SegmentInterface extends CreatorInterface
{
    /**
     * @param SegmentReportType $reportType
     * @return SegmentReportInterface
     */
    public function doCreateReport(SegmentReportType $reportType);
}