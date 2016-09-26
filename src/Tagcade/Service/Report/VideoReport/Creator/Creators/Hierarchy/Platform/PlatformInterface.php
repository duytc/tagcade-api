<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;

interface PlatformInterface extends CreatorInterface
{
    /**
     * @param PlatformReportType $reportType
     * @return PlatformReportInterface
     */
    public function doCreateReport(PlatformReportType $reportType);
}