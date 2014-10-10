<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\AdSlotInterface as AdSlotModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AdSlotReportInterface;

interface AdSlotInterface extends ReportTypeInterface
{
    /**
     * @param AdSlotModelInterface $adSlot
     * @return AdSlotReportInterface
     */
    public function doCreateReport(AdSlotModelInterface $adSlot);
}