<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface ReportDataInterface
{
    /**
     * @return int|null
     */
    public function getTotalOpportunities();

    /**
     * @return int|null
     */
    public function getImpressions();

    /**
     * @return int|null
     */
    public function getPassbacks();

    /**
     * @return float|null
     */
    public function getFillRate();

    /**
     * @return float|null
     */
    public function getEstRevenue();

    /**
     * @return float
     */
    public function getEstCpm();

    /**
     * @return int
     */
    public function getInBannerRequests();

    /**
     * @return int
     */
    public function getInBannerImpressions();

    /**
     * @return int
     */
    public function getInBannerTimeouts();
}