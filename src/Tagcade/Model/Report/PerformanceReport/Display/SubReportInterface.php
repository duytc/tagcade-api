<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface SubReportInterface
{
    /**
     * @return ReportInterface|null
     */
    public function getSuperReport();

    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSuperReport(ReportInterface $report);

    /**
     * @param ReportInterface $report
     * @return static
     */
    public function setSuperReport(ReportInterface $report);
}