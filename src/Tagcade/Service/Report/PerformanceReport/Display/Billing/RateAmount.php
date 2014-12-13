<?php
/**
 * Created by PhpStorm.
 * User: litpu
 * Date: 29/11/2014
 * Time: 22:05
 */

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;

class RateAmount
{
    /**
     * @var CpmRate $rate
     */
    private $rate;

    /**
     * @var float $amount
     */
    private $amount;

    function __construct(CpmRate $rate, $amount)
    {
        if (!is_numeric($amount) || $amount < 0) {
            throw new InvalidArgumentException('amount must be numeric and non-negative');
        }

        $this->rate = $rate;
        $this->amount = $amount;
    }

    /**
     * @return CpmRate
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }


}