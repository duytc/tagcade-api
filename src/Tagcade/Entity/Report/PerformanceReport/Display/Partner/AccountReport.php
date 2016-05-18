<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReport as AccountReportModel;

/**
 * Contains real ad network partner performance report for a publisher
 * This is automatically created from Hierarchy AdNetwork!!!
 * @package Tagcade\Entity\Report\PerformanceReport\Display\Partner
 */
class AccountReport extends AccountReportModel
{
    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $publisher;
}