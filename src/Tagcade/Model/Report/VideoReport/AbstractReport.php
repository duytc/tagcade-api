<?php


namespace Tagcade\Model\Report\VideoReport;


use DateTime;
use Tagcade\Model\Report\CalculateRatiosTrait;

abstract class AbstractReport implements ReportInterface
{
    use CalculateRatiosTrait;

    protected $id;
    protected $date;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $fillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $blocks;

    /**
     * @param int $bids
     * @return $this
     */
    public function setBids($bids)
    {
        $this->bids = (int)$bids;
        return $this;
    }

    /**
     * @param int $clicks
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->clicks = (int)$clicks;
        return $this;
    }

    /**
     * @param int $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param int $impressions
     * @return $this
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @param int $requests
     * @return $this
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return $this
     */
    public function setDate(DateTime $date = null)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return float
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return int
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return float
     */
    public function getErrorRate()
    {
        return $this->errorRate;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return float
     */
    public function getClickThroughRate()
    {
        return $this->clickThroughRate;
    }

    /**
     * @return int
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @return float
     */
    public function getBidRate()
    {
        return $this->bidRate;
    }


    public function setCalculatedFields()
    {
        $this->setFillRate();
        $this->setBidRate();
        $this->setErrorRate();
        $this->setClickThroughRate();
    }

    public function setClickThroughRate()
    {
        $this->clickThroughRate = $this->getRatio($this->getClicks(), $this->getImpressions());
        return $this;
    }

    public function setBidRate()
    {
        $this->bidRate = $this->calculateBidRate();
        return $this;
    }

    public function setErrorRate()
    {
        $this->errorRate = $this->calculateErrorRate();
        return $this;
    }

    public function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param mixed $blocks
     * @return $this
     */

    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
        return $this;
    }

    abstract protected function calculateFillRate();

    abstract protected function calculateErrorRate();

    abstract protected function calculateBidRate();
}