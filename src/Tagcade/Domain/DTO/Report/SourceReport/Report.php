<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

use Tagcade\Model\Report\SourceReport\Report as ReportModel;

class Report
{
    protected $report;
    protected $records;
    protected $viewsPerVisit;

    public function __construct(ReportModel $report, array $records)
    {
        $this->report = $report;
        $this->records = $records;

        if ( null !== $report && $report->getVisits() != 0) {
            $this->viewsPerVisit = (float)$report->getPageViews() / $report->getVisits();
        }
    }

    /**
     * @return ReportModel
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return float
     */
    public function getViewsPerVisit()
    {
        return $this->viewsPerVisit;
    }


}