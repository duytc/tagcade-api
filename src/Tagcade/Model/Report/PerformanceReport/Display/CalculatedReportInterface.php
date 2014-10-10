<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;

interface CalculatedReportInterface extends ReportInterface
{
    /**
     * @return int|null
     */
    public function getSlotOpportunities();

    /**
     * @return int|null
     */
    public function getTotalOpportunities();

    /**
     * @return ArrayCollection
     */
    public function getSubReports();

    /**
     * @param ReportInterface $report
     * @return static
     */
    public function addSubReport(ReportInterface $report);

    /**
     * Indicates whether the supplied report is a valid sub report for this report
     *
     * i.e an AdSlotReport only allows AdTagReports
     *
     * @param ReportInterface ReportInterface
     * @return boolean
     */
    public function isValidSubReport(ReportInterface $report);
}