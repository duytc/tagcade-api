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
    public function getBilledAmount();

    public function getHbRequests();

    public function getHbBilledAmount();

    public function getRtbImpressions();
}