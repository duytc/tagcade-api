<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Model\Report\RtbReport\Hierarchy\SiteReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Site as SiteReportType;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

interface RtbSiteSnapshotInterface extends RtbSnapshotCreatorInterface
{
    /**
     * @param SiteReportType $reportType
     * @return SiteReportInterface
     */
    public function doCreateReport(SiteReportType $reportType);
}