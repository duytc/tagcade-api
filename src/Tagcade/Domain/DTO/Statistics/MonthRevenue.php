<?php

namespace Tagcade\Domain\DTO\Statistics;

use DateTime;

class MonthRevenue {

    private $month;
    private $revenue;

    function __construct(DateTime $month, $revenue)
    {
        $this->month = $month;
        $this->revenue = $revenue;
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
    public function getRevenue()
    {
        return $this->revenue;
    }


} 