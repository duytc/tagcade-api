<?php

namespace Tagcade\Model\Report\RtbReport;

use Tagcade\Model\Report\CalculateRatiosTrait;
use DateTime;

abstract class  AbstractReport implements ReportInterface
{
    use CalculateRatiosTrait;

    protected $id;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @inheritdoc
     */
    public function setOpportunities($opportunity)
    {
        $this->opportunities = (int) $opportunity;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @inheritdoc
     */
    public function setImpressions($impressions)
    {
        $this->impressions = (int) $impressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEarnedAmount()
    {
        return $this->earnedAmount;
    }

    /**
     * @param mixed $earnedAmount
     * @return self
     */
    public function setEarnedAmount($earnedAmount)
    {
        $this->earnedAmount = $earnedAmount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @inheritdoc
     */
    public function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();

        return $this;
    }

    /**
     * @return float
     */
    abstract protected function calculateFillRate();

    /**
     * @inheritdoc
     */
    public function setCalculatedFields()
    {
        $this->setFillRate();

        if ($this->getName() === null) {
            $this->setDefaultName();
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    protected function setDefaultName()
    {
        // do nothing by default
    }
}