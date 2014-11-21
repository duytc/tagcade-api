<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Query;

class QueryFactory
{
    public static function createQuery($reportType, $startDate = null, $endDate = null, $expanded = false, $grouped = false)
    {
        if (is_array($reportType)) {
            return new MultipleReportTypeQuery($reportType, $startDate, $endDate, $expanded, $grouped);
        }

        return new SingleReportTypeQuery($reportType, $startDate, $endDate, $expanded, $grouped);
    }
}