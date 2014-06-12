<?php

namespace Tagcade\Repository\Report\SourceReport;

use Tagcade\Model\Report\SourceReport\Report;
use DateTime;

interface ReportRepositoryInterface
{
    /**
     * @param int $siteId
     * @param DateTime|null $dateFrom a datetime
     * @param DateTime|null $dateTo a datetime or null for no date range
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param string|null $sortField
     * @return array|bool
     */
    public function getReports($siteId, DateTime $dateTo, DateTime $dateFrom = null, $rowOffset = null, $rowLimit = null, $sortField = null);

    /**
     * @param int $reportId
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param null $sortField
     * @return Report|boolean
     */
    public function getReport($reportId, $rowOffset = null, $rowLimit = null, $sortField = null);
}