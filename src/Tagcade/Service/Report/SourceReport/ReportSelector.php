<?php

namespace Tagcade\Service\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\DateUtil;
use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Domain\DTO\Report\SourceReport\Report as ReportDTO;

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
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate = null, $rowOffset = 0, $rowLimit = 200)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        $rowOffset = intval($rowOffset);
        $rowLimit = intval($rowLimit);

        $reportSubset = [];

        $reports = $this->repository->getReports($site, $startDate, $endDate);

        foreach($reports as $report) {
            $reportSubset[] = new ReportDTO(
                $report->getDate(),
                $report->getSiteId(),
                $report->getRecords()->slice($rowOffset, $rowLimit)
            );
        }

        return $reportSubset;
    }

} 