<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;

interface ReportRepositoryInterface
{
    /**
     * @param string $domain
     * @param DateTime|null $dateTo a datetime
     * @param DateTime|null $dateFrom a datetime or null for no date range
     * @param int $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @return array
     */
    public function getReports($domain, DateTime $dateTo, DateTime $dateFrom = null, $rowLimit);
}