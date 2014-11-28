<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRatiosTrait;

use DateTime;

abstract class AbstractReport implements ReportInterface
{
    use CalculateRatiosTrait;

    protected $id;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

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
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function setTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities = (int) $totalOpportunities;

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
     * @inheritdoc
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @inheritdoc
     */
    public function setPassbacks($passbacks)
    {
        $this->passbacks = (int) $passbacks;

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
    protected function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();
    }

    /**
     * @return float
     */
    abstract protected function calculateFillRate();

    /**
     * @inheritdoc
     */
    public function getEstRevenue()
    {
        return $this->estRevenue;
    }

    /**
     * @inheritdoc
     */
    public function setEstRevenue($estRevenue)
    {
        $this->estRevenue = $estRevenue;

        return $this;
    }

    /**
     * @return float
     */
    public function getEstCpm()
    {
        return $this->estCpm;
    }

    /**
     * @param float $estCpm
     * @return $this
     */
    public function setEstCpm($estCpm)
    {
        $this->estCpm = $estCpm;

        return $this;
    }

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