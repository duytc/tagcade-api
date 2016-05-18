<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\SubPublisher;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReport as SubPublisherAdNetworkReportModel;

class SubPublisherAdNetworkReport extends SubPublisherAdNetworkReportModel
{
    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $adNetwork;
    protected $subPublisher;
}