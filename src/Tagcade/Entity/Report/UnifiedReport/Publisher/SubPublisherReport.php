<?php


namespace Tagcade\Entity\Report\UnifiedReport\Publisher;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherReport as SubPublisherReportModel;

class SubPublisherReport extends SubPublisherReportModel
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
}