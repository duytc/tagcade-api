<?php

namespace Tagcade\Model\Report\RtbReport;

use Doctrine\Common\Collections\ArrayCollection;

interface SuperReportInterface
{
    /**
     * @return ArrayCollection|array
     */
    public function getSubReports();

    /**
     * @param ReportInterface $report
     * @return static
     */
    public function addSubReport(ReportInterface $report);

    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report);
}