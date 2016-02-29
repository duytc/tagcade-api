<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\RtbReport\SubReportInterface;

interface AdSlotReportInterface extends SubReportInterface
{
    /**
     * @return ReportableAdSlotInterface
     */
    public function getAdSlot();
}