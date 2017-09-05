<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use DateTime;

interface GrouperInterface
{
    /**
     * @return ReportGroup
     */
    public function getGroupedReport();

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType();

    /**
     * @return ReportDataInterface[]
     */
    public function getReports();

    /**
     * @return string
     */
    public function getReportName();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return int
     */
    public function getTotalOpportunities();

    /**
     * @return int
     */
    public function getImpressions();

    /**
     * @return int
     */
    public function getPassbacks();

    /**
     * @return float
     */
    public function getFillRate();

    /**
     * @return float
     */
    public function getEstCpm();

    /**
     * @return float
     */
    public function getEstRevenue();

    /**
     * @return float
     */
    public function getAverageEstCpm();

    /**
     * @return float
     */
    public function getAverageEstRevenue();
}