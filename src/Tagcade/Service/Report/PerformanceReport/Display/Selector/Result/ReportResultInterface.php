<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result;

use DateTime;
use IteratorAggregate;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

interface ReportResultInterface extends IteratorAggregate
{
    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
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