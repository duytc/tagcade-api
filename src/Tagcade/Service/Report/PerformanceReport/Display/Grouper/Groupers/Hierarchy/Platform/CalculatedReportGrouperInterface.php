<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\Hierarchy\Platform;


use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\GrouperInterface;

interface CalculatedReportGrouperInterface extends GrouperInterface
{

    /**
     * @return float
     */
    public function getTotalBilledAmount();

    /**
     * @return int
     */
    public function getSlotOpportunities();

} 