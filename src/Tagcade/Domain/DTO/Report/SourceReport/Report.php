<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

use Tagcade\Model\Report\SourceReport\Report as ReportModel;

class Report
{
    protected $report;
    protected $records;

    public function __construct(ReportModel $report, array $records)
    {
        $this->report = $report;
        $this->records = $records;
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
}