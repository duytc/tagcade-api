<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReport as AdNetworkDomainAdTagReportModel;

/**
 * Contains AdNetwork Site Performance report after mapping partner site domain with tagcade site domain
 * @package Tagcade\Entity\Report\PerformanceReport\Display\Partner
 */
class AdNetworkDomainAdTagReport extends AdNetworkDomainAdTagReportModel
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
    protected $partnerTagId;
    protected $adNetwork;
}