<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface as BaseReportInterface;

interface CalculatedReportInterface extends BaseReportInterface
{
    /**
     * @return int|null
     */
    public function getSlotOpportunities();

    /**
     * @param int $slotOpportunities
     * @return $this
     */
    public function setSlotOpportunities($slotOpportunities);
}