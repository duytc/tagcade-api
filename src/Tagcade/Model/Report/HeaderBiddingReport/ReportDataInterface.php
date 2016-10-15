<?php

namespace Tagcade\Model\Report\HeaderBiddingReport;

interface ReportDataInterface
{
    /**
     * @return int
     */
    public function getRequests();

    /**
     * @return float
     */
    public function getBilledRate();

    /**
     * @return float
     */
    public function getBilledAmount();
}