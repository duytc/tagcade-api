<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

interface AdSlotInterface extends CreatorInterface
{
    /**
     * @param AdSlotReportType $reportType
     * @return AdSlotReportInterface
     */
    public function doCreateReport(AdSlotReportType $reportType);
}