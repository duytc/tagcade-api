<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface BilledReportDataInterface extends ReportDataInterface
{
    /**
     * @return int|null
     */
    public function getSlotOpportunities();

    /**
     * @return float
     */
    public function getOpportunityFillRate();

    /**
     * @return float
     */
    public function getBilledAmount();

    /**
     * @return int
     */
    public function getInBannerImpressions();

    /**
     * @return int
     */
    public function getInBannerTimeouts();

    /**
     * @return float
     */
    public function getInBannerBilledRate();

    /**
     * @return float
     */
    public function getInBannerBilledAmount();

    /**
     * @return int
     */
    public function getInBannerRequests();
}