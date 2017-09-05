<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReport as AdNetworkReportModel;

class AdNetworkReport extends AdNetworkReportModel
{
    protected $adNetwork;
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
    protected $adOpportunities;

    protected $firstOpportunities;
    protected $verifiedImpressions;
    protected $unverifiedImpressions;
    protected $blankImpressions;
    protected $voidImpressions;
    protected $clicks;
    protected $networkOpportunityFillRate;

    protected $inBannerRequests;
    protected $inBannerImpressions;
    protected $inBannerTimeouts;
    protected $inBannerBilledRate;
    protected $inBannerBilledAmount;
}