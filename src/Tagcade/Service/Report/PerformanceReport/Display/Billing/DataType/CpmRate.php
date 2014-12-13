<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType;

use Tagcade\Exception\InvalidArgumentException;

class CpmRate {

    private $cpmRate;
    private $custom;

    function __construct($cpmRate, $custom = false)
    {
        if (!is_numeric($cpmRate) || $cpmRate < 0) {
            throw new InvalidArgumentException('rate must be numeric and non-negative');
        }

        $this->cpmRate = $cpmRate;
        $this->custom = $custom;
    }

    /**
     * @return mixed
     */
    public function getCpmRate()
    {
        return $this->cpmRate;
    }

    /**
     * @return boolean
     */
    public function isCustom()
    {
        return $this->custom;
    }
}