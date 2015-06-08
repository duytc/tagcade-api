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

    /**
     * @return mixed
     */
    public function getBlankImpressions()
    {
        return $this->blankImpressions;
    }


    public function setBlankImpressions($blankImpressions)
    {
        $this->blankImpressions = $blankImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstOpportunities()
    {
        return $this->firstOpportunities;
    }

    public function setFirstOpportunities($firstOpportunities)
    {
        $this->firstOpportunities = $firstOpportunities;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnverifiedImpressions()
    {
        return $this->unverifiedImpressions;
    }

    public function setUnverifiedImpressions($unverifiedImpressions)
    {
        $this->unverifiedImpressions = $unverifiedImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerifiedImpressions()
    {
        return $this->verifiedImpressions;
    }

    public function setVerifiedImpressions($verifiedImpressions)
    {
        $this->verifiedImpressions = $verifiedImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    public function setClicks($clicks)
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoidImpressions()
    {
        return $this->voidImpressions;
    }

    public function setVoidImpressions($voidImpressions)
    {
        $this->voidImpressions = $voidImpressions;

        return $this;
    }


} 