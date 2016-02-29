<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Grouper\Groupers;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\Group\ReportGroup;

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
    public function getOpportunities();

    /**
     * @return int
     */
    public function getImpressions();

    /**
     * @return float
     */
    public function getFillRate();

    /**
     * @return mixed
     */
    public function getEarnedAmount();
}