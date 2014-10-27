<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\AdTagInterface;

class AdTagReport extends AbstractReport implements AdTagReportInterface
{
    const REPORT_TYPE = 'platform.adTag';

    use SuperReportTrait;

    /**
     * @var AdTagInterface
     */
    protected $adTag;

    protected $position;

    /**
     * The relative fill rate is how many impressions were filled by this ad tag compared to other ad tags in the same ad slot
     * So it is the fill rate of this tag, relative to the fill rates of the other tags
     */
    protected $relativeFillRate;

    /**
     * @return AdTagInterface|null
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @return int|null
     */
    public function getAdTagId()
    {
        if ($this->adTag instanceof AdTagInterface) {
            return $this->adTag->getId();
        }

        return null;
    }

    /**
     * @param AdTagInterface $adTag
     * @return $this
     */
    public function setAdTag(AdTagInterface $adTag)
    {
        $this->adTag = $adTag;
        return $this;
    }

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

    /**
     * @inheritdoc
     */
    protected function setFillRate()
    {
        // note that we use slot opportunities to calculate fill rate in this Reports
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());

        return $this;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }

    protected function setDefaultName()
    {
        if ($this->adTag instanceof AdTagInterface) {
            $this->setName($this->adTag->getName());
        }
    }
}