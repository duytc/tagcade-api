<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;

interface PlatformInterface extends CreatorInterface
{
    /**
     * @param PlatformReportType $reportType
     * @return PlatformReportInterface
     */
    public function doCreateReport(PlatformReportType $reportType);
}