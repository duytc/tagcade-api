<?php

namespace Tagcade\Model\Report\RtbReport\ReportType\Hierarchy;

use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\RonAdSlotReportInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

class RonAdSlot extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'ronAdSlot';

    /** @var RonAdSlotInterface private $ronAdSlot;

    /** @var SegmentInterface */
    private $segment;

    public function __construct(RonAdSlotInterface $ronAdSlot, $segment = null)
    {
        $this->ronAdSlot = $ronAdSlot;
        $this->segment = $segment;
    }

    /**
     * @return RonAdSlotInterface
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
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    public function getSegmentId()
    {
        if (!$this->segment instanceof SegmentInterface) {
            return null;
        }

        return $this->segment->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof RonAdSlotReportInterface;
    }
}