<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\Behaviors\CalculateRatios;
use Tagcade\Model\Report\PerformanceReport\Behaviors\GenericReport;
use Tagcade\Model\Report\PerformanceReport\Behaviors\HasSuperReport;

/**
 * Ad Tag report is unique because it is the inner core report for all other reports
 * All other reports are calculated based on ad tag reports
 *
 * Ad tag reports are different because they don't contain slot opportunities, they contain the individual
 * opportunities of the tag
 *
 * For this reason, this report does not inherit a base class and is defined specifically here
 */
class AdTagReport implements AdTagReportInterface
{
    use GenericReport;
    use HasSuperReport;
    use CalculateRatios;

    protected $adTag;
    protected $position;
    protected $opportunities;
    protected $impressions;
    protected $passbacks;

    /**
     * The fill rate is the fill rate for this tag, i.e how many impressions / opportunities
     */
    protected $fillRate;

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
     * @param AdTagInterface $adTag
     * @return self
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
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @param int $opportunities
     * @return self
     */
    public function setOpportunities($opportunities)
    {
        $this->opportunities = $opportunities;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param int $impressions
     * @return self
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @param int $passbacks
     * @return self
     */
    public function setPassbacks($passbacks)
    {
        $this->passbacks = $passbacks;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFillRate()
    {
        return $this->fillRate;
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

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }

    public function setCalculatedFields()
    {
        if ($this->position === null) {
            throw new RuntimeException('position should be set for AdTagReports');
        }

        $this->setFillRate();

        if ($this->getName() === null) {
            $this->setDefaultName();
        }
    }

    protected function setFillRate()
    {
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getOpportunities());
    }

    protected function setDefaultName()
    {
        if ($adTag = $this->getAdTag()) {
            $this->setName($adTag->getName());
        }
    }
}