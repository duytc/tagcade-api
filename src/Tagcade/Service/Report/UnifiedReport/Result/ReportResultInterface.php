<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use DateTime;
use IteratorAggregate;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

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

    /**
     * @return int
     */
    public function getTotalRecord();


    /**
     * @return mixed
     */
    public function getPaidImps();

    /**
     * @return mixed
     */
    public function getTotalImps();

    /**
     * @return mixed
     */
    public function getFillRate();

    /**
     * @return mixed
     */
    public function getAverageFillRate();

    /**
     * @return mixed
     */
    public function getAverageTotalImps();

    /**
     * @return mixed
     */
    public function getAveragePaidImps();
}