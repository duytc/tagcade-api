<?php


namespace Tagcade\Entity\Report\UnifiedReport\Network;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagReport as NetworkDomainAdTagReportModel;

class NetworkDomainAdTagReport extends NetworkDomainAdTagReportModel
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
    protected $domain;
}