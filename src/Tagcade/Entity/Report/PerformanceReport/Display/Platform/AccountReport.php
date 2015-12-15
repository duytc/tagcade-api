<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReport as AccountReportModel;

class AccountReport extends AccountReportModel
{
    protected $publisher;
    protected $superReport;
    protected $subReports;
    protected $slotOpportunities;
    protected $billedAmount;
    protected $billedRate;
    protected $id;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;
}