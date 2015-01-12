<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

use DateTime;

class ReportCollection
{
    protected $startDate;
    protected $endDate;
    protected $siteId;
    protected $reports;
    protected $reportGroup;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $siteId
     * @param Report[] $reports
     * @param ReportGroup $reportGroup
     */
    public function __construct(DateTime $startDate, DateTime $endDate, $siteId, array $reports, ReportGroup $reportGroup)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->siteId = $siteId;
        $this->reports = $reports;
        $this->reportGroup = $reportGroup;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getSiteId()
    {
        return $this->siteId;
    }

    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return ReportGroup
     */
    public function getReportGroup()
    {
        return $this->reportGroup;
    }
}