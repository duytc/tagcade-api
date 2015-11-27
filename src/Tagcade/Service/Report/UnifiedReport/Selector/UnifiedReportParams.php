<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

class UnifiedReportParams extends Params
{
    /**
     * @param DateTime $startDate
     * @param DateTime|null $endDate
     * @param bool $group
     */
    function __construct(DateTime $startDate, DateTime $endDate = null, $group = false)
    {
        parent::__construct($startDate, $endDate, false, $group);
    }
}