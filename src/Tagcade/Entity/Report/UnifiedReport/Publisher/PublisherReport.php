<?php


namespace Tagcade\Entity\Report\UnifiedReport\Publisher;
use Tagcade\Model\Report\UnifiedReport\Publisher\PublisherReport as PublisherReportModel;

class PublisherReport extends PublisherReportModel
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

    protected $publisher;
}