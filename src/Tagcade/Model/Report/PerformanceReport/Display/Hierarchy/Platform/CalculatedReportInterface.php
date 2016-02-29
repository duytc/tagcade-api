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
     * @return float
     */
    public function getBilledRate();

    /**
     * @param float $billedRate
     */
    public function setBilledRate($billedRate);

    /**
     * @param $rtbImpressions
     * @return $this
     */
    public function setRtbImpressions($rtbImpressions);

    /**
     * @return int
     */
    public function getRtbImpressions();

}