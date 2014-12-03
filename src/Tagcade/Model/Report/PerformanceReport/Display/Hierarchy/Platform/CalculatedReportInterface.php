<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface CalculatedReportInterface extends ReportInterface, BilledReportDataInterface
{
    /**
     * @param int $slotOpportunities
     * @return $this
     */
    public function setSlotOpportunities($slotOpportunities);

    /**
     * @param float $billedAmount
     * @return $this
     */
    public function setBilledAmount($billedAmount);

    /**
     * @param float $billedRate
     */
    public function setBilledRate($billedRate);
}