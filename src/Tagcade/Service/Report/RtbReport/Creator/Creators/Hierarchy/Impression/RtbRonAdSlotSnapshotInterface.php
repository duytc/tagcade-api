<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\RonAdSlot as RonAdSlotReportType;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

interface RtbRonAdSlotSnapshotInterface extends RtbSnapshotCreatorInterface
{
    /**
     * @param RonAdSlotReportType $reportType
     * @return AdSlotReportInterface
     */
    public function doCreateReport(RonAdSlotReportType $reportType);
}