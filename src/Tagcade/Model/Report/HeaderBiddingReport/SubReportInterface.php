<?php

namespace Tagcade\Model\Report\HeaderBiddingReport;

interface SubReportInterface
{
    /**
     * @return ReportInterface|null
     */
    public function getSuperReport();

    public function getSuperReportId();

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