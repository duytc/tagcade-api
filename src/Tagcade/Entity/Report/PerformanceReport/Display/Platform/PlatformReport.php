<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReport as PlatformReportModel;

class PlatformReport extends PlatformReportModel
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
    protected $slotOpportunities;
    protected $billedAmount;
    protected $billedRate;
    protected $subReports;
}