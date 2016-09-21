<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as WaterfallTagReportType;

interface WaterfallTagInterface extends CreatorInterface
{
    /**
     * @param WaterfallTagReportType $reportType
     * @return WaterfallTagReportInterface
     */
    public function doCreateReport(WaterfallTagReportType $reportType);
}