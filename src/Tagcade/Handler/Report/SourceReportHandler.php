<?php

namespace Tagcade\Handler\Report;

use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\Reporting\ReportUtilInterface;
use Tagcade\Model\Report\SourceReport\Report;
use DateTime;

class SourceReportHandler
{
    private $repository;
    private $reportUtil;

    public function __construct(ReportRepositoryInterface $repository, ReportUtilInterface $reportUtil)
    {
        $this->repository = $repository;
        $this->reportUtil = $reportUtil;
    }

    /**
     * @param string $domain
     * @param int|DateTime|null $dateFrom date in format of YYMMDD, a datetime or null for the current date
     * @param int|DateTime|null $dateTo date in format of YYMMDD, a datetime or null for no date range
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param string|null $sortField
     * @return array
     */
    public function getReports($domain, $dateFrom = null, $dateTo = null, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        $dateFrom = $this->reportUtil->getDateTime($dateFrom, $todayIfEmpty = true);
        $dateTo = $this->reportUtil->getDateTime($dateTo);

        return $this->repository->getReports($domain, $dateFrom, $dateTo, $rowOffset, $rowLimit, $sortField);
    }

    /**
     * @param int $reportId
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param string|null $sortField
     * @return Report|boolean;
     */
    public function getReport($reportId, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        return $this->repository->getReport($reportId, $rowOffset, $rowLimit, $sortField);
    }
}