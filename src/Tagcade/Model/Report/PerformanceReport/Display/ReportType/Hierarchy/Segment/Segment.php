<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment;

use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\SegmentReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\CalculatedReportTypeInterface;

class Segment extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.segment';

    /**
     * @var SegmentInterface
     */
    private $segment;

    public function __construct(SegmentInterface $segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SegmentReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof RonAdSlotReportInterface;
    }
}