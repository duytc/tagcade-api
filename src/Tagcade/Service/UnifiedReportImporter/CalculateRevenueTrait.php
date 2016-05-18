<?php

namespace Tagcade\Service\UnifiedReportImporter;


trait CalculateRevenueTrait
{
    static $REVENUE_OPTION_NONE = 0;
    static $REVENUE_OPTION_CPM_FIXED = 1;
    static $REVENUE_OPTION_CPM_PERCENT = 2;

    protected function calculateRevenue($revenue, $impression, $revenueConfigOption = 0, $revenueConfigValue = 0)
    {
        switch ($revenueConfigOption) {
            case static::$REVENUE_OPTION_NONE:
                return $revenue;
            case static::$REVENUE_OPTION_CPM_PERCENT:
                return $revenueConfigValue  * $revenue / 100;
            case static::$REVENUE_OPTION_CPM_FIXED:
                return $this->calculateRevenueByCpm($revenueConfigValue, $impression);
        }

        throw new \Exception('Invalid revenue share config');
    }

    protected function calculateRevenueByCpm($cpm, $impression)
    {
        return $cpm * $impression / 1000;
    }

    protected function calculateCpm($revenue, $impression)
    {
        return $impression > 0 ? (1000 * $revenue) / $impression : 0;
    }
} 