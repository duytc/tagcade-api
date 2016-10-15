<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SubReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

interface AdSlotReportInterface extends SubReportInterface, ReportInterface
{
    /**
     * @return ReportableAdSlotInterface
     */
    public function getAdSlot();
}