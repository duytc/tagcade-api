<?php


namespace Tagcade\Entity\Report\UnifiedReport\Network;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReport as NetworkSiteReportModel;

class NetworkSiteReport extends NetworkSiteReportModel
{
    protected $id;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $adNetwork;
    protected $domain;
}