<?php

namespace Tagcade\Domain\DTO\Report\RtbReport;


interface RtbRedisReportDataInterface
{
    /**
     * @return int
     */
    public function getSlotOpportunities();

    /**
     * @return int
     */
    public function getImpressions();

    /**
     * @return float
     */
    public function getPrice();
} 