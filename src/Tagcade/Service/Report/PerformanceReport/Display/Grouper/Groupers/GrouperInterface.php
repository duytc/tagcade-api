<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use DateTime;

interface GrouperInterface
{
    /**
     * @return ReportGroup
     */
    public function getGroupedReport();

    /**
     * @return ReportTypeInterface
     */
    public function getReportType();

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
}