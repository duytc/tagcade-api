<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Result;


use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

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

    /**
     * @return string|null
     */
    public function getName();
}