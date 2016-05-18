<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReport as AdNetworkDomainReportModel;

/**
 * Contains AdNetwork Site Performance report after mapping partner site domain with tagcade site domain
 * @package Tagcade\Entity\Report\PerformanceReport\Display\Partner
 */
class AdNetworkDomainReport extends AdNetworkDomainReportModel
{
    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $domain;
    protected $adNetwork;
}