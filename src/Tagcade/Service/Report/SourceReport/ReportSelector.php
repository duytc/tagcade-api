<?php

namespace Tagcade\Service\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\DateUtil;

class ReportSelector implements ReportSelectorInterface
{
    /**
     * @var ReportRepositoryInterface
     */
    protected $repository;

    /**
     * @var DateUtil
     */
    protected $dateUtil;

    /**
     * @param ReportRepositoryInterface $repository
     * @param DateUtil $dateUtil
     */
    public function __construct(ReportRepositoryInterface $repository, DateUtil $dateUtil)
    {
        $this->repository = $repository;
        $this->dateUtil = $dateUtil;
    }

    /**
     * @inheritdoc
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate, $rowOffset = 0, $rowLimit = 200)
    {
        $reports = [];

        foreach($this->dateUtil->getPeriodOneDay($startDate, $endDate) as $curDate) {
            if( $report = $this->repository->getReport($site, $curDate, $rowOffset, $rowLimit)) {
                $reports = array_merge($reports, $report);
            }
        }

        return $reports;
    }

} 