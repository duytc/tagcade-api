<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Grouper\Groupers;

use Tagcade\Model\Report\HeaderBiddingReport\ReportDataInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\Group\ReportGroup;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
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
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();
}