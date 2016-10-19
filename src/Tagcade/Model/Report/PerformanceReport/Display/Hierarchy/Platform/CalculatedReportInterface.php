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
     * @return float
     */
    public function getInBannerBilledRate();

    /**
     * @param float $inBannerBilledRate
     * @return self
     */
    public function setInBannerBilledRate($inBannerBilledRate);

    /**
     * @return float
     */
    public function getInBannerBilledAmount();

    /**
     * @param float $inBannerBilledAmount
     * @return self
     */
    public function setInBannerBilledAmount($inBannerBilledAmount);

    /**
     * @param int $inBannerImpressions
     * @return self
     */
    public function setInBannerImpressions($inBannerImpressions);

    /**
     * @param int $inBannerTimeouts
     * @return self
     */
    public function setInBannerTimeouts($inBannerTimeouts);

    /**
     * @param int $inBannerRequests
     * @return self
     */
    public function setInBannerRequests($inBannerRequests);

    /**
     * @param int $rtbImpressions
     * @return $this
     */
    public function setRtbImpressions($rtbImpressions);

    /**
     * @return int
     */
    public function getRtbImpressions();

    public function setThresholdBilledAmount($chainToSubReports = true);
}