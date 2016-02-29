<?php

namespace Tagcade\Model\Report\RtbReport\ReportType;


use Tagcade\Model\Report\RtbReport\ReportInterface;

interface CalculatedReportTypeInterface extends ReportTypeInterface
{
    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report);
}