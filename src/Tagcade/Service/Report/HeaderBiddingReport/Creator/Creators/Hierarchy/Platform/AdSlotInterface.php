<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

interface AdSlotInterface extends CreatorInterface
{
    /**
     * @param AdSlotReportType $reportType
     * @return AdSlotReportInterface
     */
    public function doCreateReport(AdSlotReportType $reportType);
}