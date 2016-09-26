<?php

namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform\Fields;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRatiosTrait;

trait VideoWaterfallTagTrait
{
    use CalculateRatiosTrait;

    /**
     * @var float
     */
    protected $billedRate;

    /**
     * @var float
     */
    protected $billedAmount;

    /**
     * @var int
     */
    protected $adTagRequests;

    /**
     * @var int
     */
    protected $adTagBids;

    /**
     * @var int
     */
    protected $adTagErrors;

    /**
     * @return int
     */
    public function getAdTagRequests()
    {
        return $this->adTagRequests;
    }

    /**
     * @param int $adTagRequests
     * @return self
     */
    public function setAdTagRequests($adTagRequests)
    {
        $this->adTagRequests = $adTagRequests;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdTagBids()
    {
        return $this->adTagBids;
    }

    /**
     * @param int $adTagBids
     * @return self
     */
    public function setAdTagBids($adTagBids)
    {
        $this->adTagBids = $adTagBids;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdTagErrors()
    {
        return $this->adTagErrors;
    }

    /**
     * @param int $adTagErrors
     * @return self
     */
    public function setAdTagErrors($adTagErrors)
    {
        $this->adTagErrors = $adTagErrors;
        return $this;
    }

    /**
     * @return float
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @param float $billedRate
     * @return self
     */
    public function setBilledRate($billedRate)
    {
        $this->billedRate = $billedRate;
        return $this;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @param float $billedAmount
     * @return self
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getAdTagRequests() === null) {
            throw new RuntimeException('ad tag request must be defined to calculate fill rates');
        }

        // note that we use ad tag request to calculate fill rate in this Reports except for DemandAdTagReport
        return $this->getPercentage($this->getImpressions(), $this->getAdTagRequests());
    }

    protected function calculateErrorRate()
    {
        if ($this->getAdTagBids() === null) {
            throw new RuntimeException('ad tag bids must be defined to calculate error rates');
        }

        // note that we use ad tag error to calculate error rate in this Reports except for DemandAdTagReport
        return $this->getPercentage($this->getAdTagErrors(), $this->getAdTagBids());
    }

    protected function calculateBidRate()
    {
        if ($this->getAdTagRequests() === null) {
            throw new RuntimeException('ad tag errors must be defined to calculate error rates');
        }

        // note that we use ad tag error to calculate error rate in this Reports except for DemandAdTagReport
        return $this->getPercentage($this->getAdTagBids(), $this->getAdTagRequests());
    }

    /**
     * @return int|null
     */
    abstract public function getImpressions();

    /**
     * @return int|null
     */
    abstract protected function getBids();
}