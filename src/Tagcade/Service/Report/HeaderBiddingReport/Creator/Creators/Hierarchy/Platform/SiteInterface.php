<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Site as SiteReportType;

interface SiteInterface extends CreatorInterface
{
    /**
     * @param SiteReportType $reportType
     * @return SiteReportInterface
     */
    public function doCreateReport(SiteReportType $reportType);
}