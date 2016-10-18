<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Result;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\ReportDataInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;

interface ReportResultInterface extends \IteratorAggregate
{
    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType();

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @return ReportDataInterface[]
     */
    public function getReports();

    public function getName();
}