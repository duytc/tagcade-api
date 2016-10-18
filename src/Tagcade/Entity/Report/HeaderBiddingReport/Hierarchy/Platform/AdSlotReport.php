<?php

namespace Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReport as AdSlotReportModel;

class AdSlotReport extends AdSlotReportModel
{
    protected $adSlot;
    protected $id;
    protected $name;
    protected $date;
    protected $billedRate;
    protected $billedAmount;
    protected $superReport;
}