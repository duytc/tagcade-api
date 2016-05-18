<?php


namespace Tagcade\Entity\Report\UnifiedReport\Network;

use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport as NetworkAdTagSubPublisherReportModel;

class NetworkAdTagSubPublisherReport extends NetworkAdTagSubPublisherReportModel
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
    protected $partnerTagId;
    protected $subPublisher;
}