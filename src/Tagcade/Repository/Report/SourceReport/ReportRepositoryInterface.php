<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;

interface ReportRepositoryInterface
{
    /**
     * @param string $domain
     * @param DateTime|null $dateFrom a datetime
     * @param DateTime|null $dateTo a datetime or null for no date range
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param string|null $sortField
     * @return array
     */
    public function getReports($domain, DateTime $dateTo, DateTime $dateFrom = null, $rowOffset = null, $rowLimit = null, $sortField = null);
}