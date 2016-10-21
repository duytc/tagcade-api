<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReport as SiteReportModel;

class SiteReport extends SiteReportModel
{
    protected $site;
    protected $superReport;
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
    protected $billedAmount;
    protected $billedRate;
    protected $rtbImpressions;

    protected $inBannerRequests;
    protected $inBannerImpressions;
    protected $inBannerTimeouts;
    protected $inBannerBilledRate;
    protected $inBannerBilledAmount;
}