<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Fields;


trait ImpressionBreakdownTrait
{
    protected $firstOpportunities;
    protected $verifiedImpressions;
    protected $unverifiedImpressions;
    protected $blankImpressions;
    protected $voidImpressions;
    protected $clicks;
    protected $refreshes; // opp on refreshes

    /**
     * @return int
     */
    public function getBlankImpressions()
    {
        return $this->blankImpressions;
    }

    /**
     * @param $blankImpressions
     * @return $this
     */
    public function setBlankImpressions($blankImpressions)
    {
        $this->blankImpressions = (int)$blankImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getFirstOpportunities()
    {
        return $this->firstOpportunities;
    }

    /**
     * @param $firstOpportunities
     * @return $this
     */
    public function setFirstOpportunities($firstOpportunities)
    {
        $this->firstOpportunities = (int)$firstOpportunities;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnverifiedImpressions()
    {
        return $this->unverifiedImpressions;
    }

    /**
     * @param $unverifiedImpressions
     * @return $this
     */
    public function setUnverifiedImpressions($unverifiedImpressions)
    {
        $this->unverifiedImpressions = (int)$unverifiedImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerifiedImpressions()
    {
        return $this->verifiedImpressions;
    }

    /**
     * @param $verifiedImpressions
     * @return $this
     */
    public function setVerifiedImpressions($verifiedImpressions)
    {
        $this->verifiedImpressions = (int)$verifiedImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @param $clicks
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->clicks = (int)$clicks;

        return $this;
    }

    /**
     * @return int
     */
    public function getVoidImpressions()
    {
        return $this->voidImpressions;
    }

    /**
     * @param $voidImpressions
     * @return $this
     */
    public function setVoidImpressions($voidImpressions)
    {
        $this->voidImpressions = (int)$voidImpressions;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshes()
    {
        return $this->refreshes;
    }

    /**
     * @inheritdoc
     */
    public function setRefreshes($refreshes)
    {
        $this->refreshes = $refreshes;

        return $this;
    }
}