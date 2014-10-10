<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface AdSlotReportInterface extends CalculatedReportWithSuperInterface
{
    /**
     * Very important to set the slot opportunities
     *
     * It's required to calculate the relative fill rate for ad tag reports
     *
     * @param int $slotOpportunities
     * @return self
     */
    public function setSlotOpportunities($slotOpportunities);
}
