<?php

namespace Tagcade\Handler\Report;

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
     * @param string $domain
     * @param int|DateTime|null $dateTo date in format of YYMMDD, a datetime or null for the current date
     * @param int|DateTime|null $dateFrom date in format of YYMMDD, a datetime or null for no date range
     * @param int $rowLimit Limit the amount of rows returned in the report, -1 for no limit
     * @return array
     */
    public function getReports($domain, $dateTo = null, $dateFrom = null, $rowLimit = -1)
    {
        $dateTo = $this->reportUtil->getDateTime($dateTo, true);
        $dateFrom = $this->reportUtil->getDateTime($dateFrom);

        return $this->repository->getReports($domain, $dateTo, $dateFrom, $rowLimit);
    }
}