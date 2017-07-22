<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReport as AdSlotReportModel;

class AdSlotReport extends AdSlotReportModel
{
    protected $adSlot;
    protected $customRate;
    protected $subReports;
    protected $id;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;
    protected $slotOpportunities;
    protected $billedRate;
    protected $billedAmount;
    protected $adOpportunities;

    protected $inBannerRequests;
    protected $inBannerImpressions;
    protected $inBannerTimeouts;
    protected $inBannerBilledRate;
    protected $inBannerBilledAmount;
}