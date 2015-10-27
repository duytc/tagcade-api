<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment;

use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\CalculatedReportTypeInterface;

class RonAdSlot extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.ronAdSlot';

    /**
     * @var RonAdSlotInterface
     */
    protected $ronAdSlot;
    /**
     * @var
     */
    private $segment;

    public function __construct(RonAdSlotInterface $ronAdSlot, $segment = null)
    {
        $this->ronAdSlot = $ronAdSlot;
        $this->segment = $segment;
    }

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
        return $this->ronAdSlot->getId();
    }

    public function getRonAdSlotType()
    {
        return $this->ronAdSlot->getLibraryAdSlot()->getLibType();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof RonAdSlotReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof RonAdTagReportInterface;
    }

    /**
     * @return SegmentInterface|null
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param mixed $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }
}