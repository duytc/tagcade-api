<?php

namespace Tagcade\Handler\Handlers\Report;

use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\Reporting\ReportUtilInterface;
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
     * @param int $siteId
     * @param int|DateTime|null $dateFrom date in format of YYMMDD, a datetime or null for yesterday's date
     * @param int|DateTime|null $dateTo date in format of YYMMDD, a datetime or null for no date range
     * @param int|null $rowOffset
     * @param int|null $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @param string|null $sortField
     * @return array
     */
    public function getReports($siteId, $dateFrom = null, $dateTo = null, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        $dateFrom = $this->reportUtil->getDateTime($dateFrom);

        if (!$dateFrom) {
            $dateFrom = new DateTime('yesterday');
        }

        $dateTo = $this->reportUtil->getDateTime($dateTo);

        return $this->repository->getReports($siteId, $dateFrom, $dateTo, $rowOffset, $rowLimit, $sortField);
    }
}