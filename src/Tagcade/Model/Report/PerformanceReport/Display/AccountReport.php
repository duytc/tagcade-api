<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    protected $publisher;

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }
}