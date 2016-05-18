<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReport as AdNetworkAdTagReportModel;

/**
 * Contains AdNetwork Ad Tag Performance report after mapping partner tag id with tagcade tag id
 * @package Tagcade\Entity\Report\PerformanceReport\Display\Partner
 */
class AdNetworkAdTagReport extends AdNetworkAdTagReportModel
{
    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $partnerTagId;
    protected $adNetwork;
}
