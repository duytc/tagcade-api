<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface ReportResultInterface
{
    /**
     * @return ReportTypeInterface
     */
    public function getReportType();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return ReportInterface[]
     */
    public function getReports();
} 