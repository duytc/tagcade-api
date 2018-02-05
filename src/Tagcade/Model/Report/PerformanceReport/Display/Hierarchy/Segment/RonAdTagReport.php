<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\RonAdTagInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\ImpressionBreakdownTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class RonAdTagReport extends AbstractReport implements RonAdTagReportInterface, ImpressionBreakdownReportDataInterface
{
    use SuperReportTrait;
    use CalculateRevenueTrait;
    use ImpressionBreakdownTrait;

    /** @var RonAdTagInterface */
    protected $ronAdTag;

    /** @var SegmentInterface */
    protected $segment;

    protected $position;

    /**
     * The relative fill rate is how many impressions were filled by this ad tag compared to other ad tags in the same ad slot
     * So it is the fill rate of this tag, relative to the fill rates of the other tags
     */
    protected $relativeFillRate;

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * It is important to record the position of the ad tag on the day on this report
     * so we can show the correct ad tag order
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelativeFillRate()
    {
        return $this->relativeFillRate;
    }

    /**
     * @inheritdoc
     */
    public function setRelativeFillRate($totalOpportunities)
    {
        $this->relativeFillRate = $this->getPercentage($this->getImpressions(), $totalOpportunities);

        return $this;
    }

    public function setCalculatedFields()
    {
        $sellPrice = $this->getAdTagSellPrice($this->getRonAdTag());
        $estRevenue = $this->calculateEstRevenue($this->getAdOpportunities(), $sellPrice);
        $this->setEstRevenue($estRevenue);

        parent::setCalculatedFields();
    }

    /**
     * @return RonAdTagInterface
     */
    public function getRonAdTag()
    {
        return $this->ronAdTag;
    }

    public function getRonAdTagId()
    {
        if ($this->ronAdTag instanceof RonAdTagInterface) {
            return $this->ronAdTag->getId();
        }

        return null;
    }

    /**
     * @param RonAdTagInterface $ronAdTag
     * @return self
     */
    public function setRonAdTag($ronAdTag)
    {
        $this->ronAdTag = $ronAdTag;
        if (null === $this->getName() && $ronAdTag instanceof RonAdTagInterface) {
            $this->setName($ronAdTag->getLibraryAdTag()->getName());
        }

        return $this;
    }

    /**
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getTotalOpportunities() === null) {
            throw new RuntimeException('total opportunities must be defined to calculate ad tag fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports
        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof RonAdSlotReportInterface;
    }

    protected function setDefaultName()
    {
        if ($this->ronAdTag instanceof RonAdTagInterface) {
            $this->setName($this->ronAdTag->getLibraryAdTag()->getName());
        }
    }
}