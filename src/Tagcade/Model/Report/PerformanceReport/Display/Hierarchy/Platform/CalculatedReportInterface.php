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
     * @param int $rtbImpressions
     * @return $this
     */
    public function setRtbImpressions($rtbImpressions);

    /**
     * @return int
     */
    public function getRtbImpressions();

    /**
     * @return int
     */
    public function getHbRequests();

    /**
     * @param int $hbRequests
     * @return self
     */
    public function setHbRequests($hbRequests);

    /**
     * @return float
     */
    public function getHbBilledRate();

    /**
     * @param float $hbBilledRate
     * @return self
     */
    public function setHbBilledRate($hbBilledRate);

    /**
     * @return float
     */
    public function getHbBilledAmount();

    /**
     * @param float $hbBilledAmount
     * @return self
     */
    public function setHbBilledAmount($hbBilledAmount);

    public function setThresholdBilledAmount($chainToSubReports = true);
}