<?php

namespace Tagcade\Domain\DTO\Statistics;

use DateTime;

class MonthBilledAmount
{

    /**
     * @var DateTime
     */
    private $month;

    private $billedAmount;

    function __construct(DateTime $month, $billedAmount)
    {
        $this->month = $month;
        $this->billedAmount = $billedAmount;
    }

    /**
     * @return DateTime
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }
}