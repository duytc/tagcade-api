<?php

namespace Tagcade\Entity\Report\UnifiedReport\AdMeta;


use Tagcade\Model\Report\UnifiedReport\AdMeta\ToTalReport as TotalReportModel;

class ToTalReport extends TotalReportModel
{
    protected $id;
    protected $publisherId;
    protected $date;
    protected $website;
    protected $webpage;
    protected $placement;
    protected $orderId;
    protected $orderNumber;
    protected $campaignName;
    protected $orderType;
    protected $clicks;
    protected $impressions;
    protected $placementImpressions;
    protected $actions;
    protected $revenue;
    //revenue-details;
    protected $impressionsRevenue;
    protected $clicksRevenue;
    protected $actionsRevenue;
    //end - revenue-details;
    protected $ctr;
    protected $ecpm;
}