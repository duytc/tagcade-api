<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment;

use Tagcade\Model\Core\RonAdTagInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class RonAdTag extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'platform.ronAdTag';

    /**
     * @var RonAdTagInterface
     */
    protected $ronAdTag;
    /**
     * @var null|SegmentInterface
     */
    private $segment;

    public function __construct(RonAdTagInterface $ronAdTag, $segment = null)
    {
        $this->ronAdTag = $ronAdTag;
        $this->segment = $segment;
    }

    /**
     * @return RonAdTagInterface
     */
    public function getRonAdTag()
    {
        return $this->ronAdTag;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof RonAdTagReportInterface;
    }

    /**
     * @return null|SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param null|SegmentInterface $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    public function getRonAdTagId()
    {
        return $this->getRonAdTag()->getId();
    }

}