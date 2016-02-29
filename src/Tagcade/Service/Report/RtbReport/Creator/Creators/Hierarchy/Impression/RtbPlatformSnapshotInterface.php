<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Model\Report\RtbReport\Hierarchy\PlatformReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Platform as PlatformReportType;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

interface RtbPlatformSnapshotInterface extends RtbSnapshotCreatorInterface
{
    /**
     * @param PlatformReportType $reportType
     * @return PlatformReportInterface
     */
    public function doCreateReport(PlatformReportType $reportType);
}