<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Model\Report\CalculateRatiosTrait;

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
    protected $adOpportunities;
    protected $supplyCost;
    protected $estProfit;

    protected $inBannerRequests;
    protected $inBannerImpressions;
    protected $inBannerTimeouts;

    public function getId()
    {
        return $this->id;
    }

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
        $this->totalOpportunities = (int)$totalOpportunities;

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
        $this->impressions = (int)$impressions;

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
        $this->passbacks = (int)$passbacks;

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

    /**
     * @inheritdoc
     */
    public function getAdOpportunities()
    {
        return $this->adOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function setAdOpportunities($adOpportunities)
    {
        $this->adOpportunities = (int)$adOpportunities;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSupplyCost()
    {
        return $this->supplyCost;
    }

    /**
     * @inheritdoc
     */
    public function setSupplyCost($supplyCost)
    {
        $this->supplyCost = $supplyCost;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEstProfit()
    {
        return $this->estProfit;
    }

    /**
     * @inheritdoc
     */
    public function setEstProfit($estProfit)
    {
        $this->estProfit = $estProfit;

        return $this;
    }

    /**
     * @return int
     */
    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    /**
     * @param int $inBannerRequests
     * @return self
     */
    public function setInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests = (int)$inBannerRequests;
        return $this;
    }

    /**
     * @return int
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    /**
     * @param int $inBannerImpressions
     * @return self
     */
    public function setInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions = (int)$inBannerImpressions;
        return $this;
    }

    /**
     * @return int
     */
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    /**
     * @param int $inBannerTimeouts
     * @return self
     */
    public function setInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts = (int)$inBannerTimeouts;
        return $this;
    }
}