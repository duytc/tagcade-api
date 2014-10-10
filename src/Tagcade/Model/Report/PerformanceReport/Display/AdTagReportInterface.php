<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

interface AdTagReportInterface extends ReportInterface
{
    /**
     * @return int|null
     */
    public function getOpportunities();

    /**
     * To calculate the relative fill rate, the total opportunities from the entire ad slot must be supplied
     *
     * @param int $totalOpportunities
     * @return self
     */
    public function setRelativeFillRate($totalOpportunities);

    public function setSuperReport(ReportInterface $report);
}