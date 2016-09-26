<?php

namespace Tagcade\Model\Report\VideoReport\ReportType;

use Tagcade\Model\Report\VideoReport\ReportInterface;

interface CalculatedReportTypeInterface extends ReportTypeInterface
{
    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report);
}