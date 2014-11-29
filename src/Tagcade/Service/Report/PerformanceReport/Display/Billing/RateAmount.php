<?php
/**
 * Created by PhpStorm.
 * User: litpu
 * Date: 29/11/2014
 * Time: 22:05
 */

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;


use Tagcade\Exception\InvalidArgumentException;

class RateAmount
{
    /**
     * @var float $rate
     */
    private $rate;

    /**
     * @var float $amount
     */
    private $amount;

    function __construct($rate, $amount)
    {
        if (!is_numeric($rate) || $rate < 0 || !is_numeric($amount) || $amount < 0) {
            throw new InvalidArgumentException('rate and amount must be numeric and non-negative');
        }

        $this->rate = $rate;
        $this->amount = $amount;
    }

    /**
     * @return float
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