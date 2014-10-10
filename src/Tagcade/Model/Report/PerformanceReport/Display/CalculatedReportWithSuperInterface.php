<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface CalculatedReportWithSuperInterface extends CalculatedReportInterface
{
    public function setSuperReport(ReportInterface $report);
}