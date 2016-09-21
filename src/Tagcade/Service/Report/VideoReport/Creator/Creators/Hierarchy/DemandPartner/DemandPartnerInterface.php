<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\DemandPartner;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReportInterface;

interface DemandPartnerInterface extends CreatorInterface
{
    /**
     * @param DemandPartnerReportType $reportType
     * @return DemandPartnerReportInterface
     */
    public function doCreateReport(DemandPartnerReportType $reportType);
}