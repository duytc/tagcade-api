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

    /**
     * @return float
     */
    public function getBilledAmount();

    /**
     * @param float $billedAmount
     * @return $this
     */
    public function setBilledAmount($billedAmount);

    /**
     * @return float
     */
    public function getBilledRate();

    /**
     * @param float $billedRate
     */
    public function setBilledRate($billedRate);
}