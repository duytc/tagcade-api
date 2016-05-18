<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\SubPublisher;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReport as SubPublisherReportModel;

class SubPublisherReport extends SubPublisherReportModel
{
    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $subPublisher;
}