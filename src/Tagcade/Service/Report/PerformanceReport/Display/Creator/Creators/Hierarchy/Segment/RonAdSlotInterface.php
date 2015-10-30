<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;

interface RonAdSlotInterface extends CreatorInterface
{
    /**
     * @param RonAdSlotReportType $reportType
     * @return AdSlotReportInterface
     */
    public function doCreateReport(RonAdSlotReportType $reportType);
}