<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface AdSlotReportInterface extends BillableInterface, CalculatedReportInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return float
     */
    public function getCustomRate();

    /**
     * @param float $customRate
     */
    public function setCustomRate($customRate);

    /**
     * @return ReportableAdSlotInterface
     */
    public function getAdSlot();


}