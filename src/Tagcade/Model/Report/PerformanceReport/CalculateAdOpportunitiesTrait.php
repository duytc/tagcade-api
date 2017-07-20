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
        return $totalOpportunities - $passbacks;
    }
}