<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\AdSlot as AdSlotReportType;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

interface RtbAdSlotSnapshotInterface extends RtbSnapshotCreatorInterface
{
    /**
     * @param AdSlotReportType $reportType
     * @return AdSlotReportInterface
     */
    public function doCreateReport(AdSlotReportType $reportType);
}