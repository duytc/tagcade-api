<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;

interface ReportRepositoryInterface
{
    /**
     * Retrieved all reports data on date time
     * @param SiteInterface $site
     * @param DateTime $date a date that report record is stored.
     * @param int $rowOffset
     * @param int $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @return array|bool
     */
    public function getReport(SiteInterface $site, DateTime $date, $rowOffset = 0, $rowLimit = 200);

}