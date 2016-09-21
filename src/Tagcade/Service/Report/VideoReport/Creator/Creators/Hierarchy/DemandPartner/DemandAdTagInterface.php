<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\DemandPartner;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandAdTagReportType;

interface DemandAdTagInterface extends CreatorInterface
{
    /**
     * @param DemandAdTagReportType $reportType
     * @return DemandAdTagReportInterface
     */
    public function doCreateReport(DemandAdTagReportType $reportType);
}