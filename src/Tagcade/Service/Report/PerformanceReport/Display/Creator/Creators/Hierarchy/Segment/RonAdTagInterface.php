<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdTagReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;

interface RonAdTagInterface extends CreatorInterface
{
    /**
     * @param RonAdTagReportType $reportType
     * @return RonAdTagReportInterface
     */
    public function doCreateReport(RonAdTagReportType $reportType);
}