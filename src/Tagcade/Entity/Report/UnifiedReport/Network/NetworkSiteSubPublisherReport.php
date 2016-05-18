<?php


namespace Tagcade\Entity\Report\UnifiedReport\Network;

use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteSubPublisherReport as NetworkSiteSubPublisherReportModel;

class NetworkSiteSubPublisherReport extends NetworkSiteSubPublisherReportModel
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
    protected $subPublisher;
}