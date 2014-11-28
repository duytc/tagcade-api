<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

class BillingRateThreshold
{
    /**
     * @var int
     */
    private $threshold;

    /**
     * @var float
     */
    private $cpmRate;

    function __construct($threshold, $cpmRate)
    {
        $this->threshold = (int)$threshold;
        $this->cpmRate = (float)$cpmRate;
    }

    /**
     * @return int
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * @return float
     */
    public function getCpmRate()
    {
        return $this->cpmRate;
    }
}