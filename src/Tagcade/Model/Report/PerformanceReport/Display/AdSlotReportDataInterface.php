<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface AdSlotReportDataInterface extends BilledReportDataInterface
{
    /**
     * @return int|null
     */
    public function getRefreshedSlotOpportunities();
}