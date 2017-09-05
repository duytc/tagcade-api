<?php

namespace Tagcade\Model\Report\PerformanceReport;

trait CalculateNetworkOpportunityFillRateTrait
{
    /**
     * @param int $adOpportunities
     * @param int $networkOpportunities
     * @return float
     */
    private function calculateNetworkOpportunityFillRate($adOpportunities, $networkOpportunities)
    {
        if ($networkOpportunities == 0) {
            // Be careful of divide by zero error
            if ($adOpportunities == 0) {
                $networkOpportunityFillRate = 0; // 0/0 = 0
            } else {
                $networkOpportunityFillRate = 1; // n/0 = 1
            }
        } else {
            $networkOpportunityFillRate = $adOpportunities / $networkOpportunities;
        }

        // Opportunity fill rate must be between 0 and 1
        $networkOpportunityFillRate = $networkOpportunityFillRate < 0 ? 0 : $networkOpportunityFillRate;
        $networkOpportunityFillRate = $networkOpportunityFillRate > 1 ? 1 : $networkOpportunityFillRate;

        return $networkOpportunityFillRate;
    }
}