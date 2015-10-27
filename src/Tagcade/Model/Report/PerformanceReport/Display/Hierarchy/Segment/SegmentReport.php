<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment;

use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class SegmentReport extends AbstractCalculatedReport implements SegmentReportInterface
{
    use SuperReportTrait;

    /**
     * @var SegmentInterface
     */
    protected $segment;


    /**
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @return int|null
     */
    public function getSegmentId()
    {
        if ($this->segment instanceof SegmentInterface) {
            return $this->segment->getId();
        }

        return null;
    }

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment(SegmentInterface $segment)
    {
        $this->segment = $segment;
        if (null === $this->getName() && $segment instanceof SegmentInterface) {
            $this->setName($segment->getName());
        }

        return $this;
    }


    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof RonAdSlotReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
    }

    protected function setDefaultName()
    {
        if ($this->segment instanceof SegmentInterface) {
            $this->setName($this->segment->getName());
        }
    }
}