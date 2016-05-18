<?php


namespace Tagcade\Entity\Report\UnifiedReport\Publisher;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherNetworkReport as SubPublisherNetworkReportModel;

class SubPublisherNetworkReport extends SubPublisherNetworkReportModel
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

    protected $subPublisher;
    protected $adNetwork;
}