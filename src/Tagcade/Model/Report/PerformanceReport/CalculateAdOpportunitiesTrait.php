<?php

namespace Tagcade\Model\Report\PerformanceReport;

trait CalculateAdOpportunitiesTrait
{
    /**
     * @param int $totalOpportunities
     * @param int $passbacks
     * @return int
     */
    private function calculateAdOpportunities($totalOpportunities, $passbacks = 0)
    {
        $adOpportunities = $totalOpportunities - $passbacks;

        return $adOpportunities > 0 ? $adOpportunities : 0;
    }

    /**
     * @param int $adOpportunities
     * @param int $slotOpportunities
     * @return float
     */
    private function calculateOpportunityFillRate($adOpportunities, $slotOpportunities)
    {
        if ($slotOpportunities == 0) {
            // Be careful of divide by zero error
            if ($adOpportunities == 0) {
                $opportunityFillRate = 0; // 0/0 = 0
            } else {
                $opportunityFillRate = 1; // n/0 = 1
            }
        } else {
            $opportunityFillRate = $adOpportunities / $slotOpportunities;
        }

        // Opportunity fill rate must be between 0 and 1
        $opportunityFillRate = $opportunityFillRate < 0 ? 0 : $opportunityFillRate;
        $opportunityFillRate = $opportunityFillRate > 1 ? 1 : $opportunityFillRate;

        return $opportunityFillRate;
    }
}