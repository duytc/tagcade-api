<?php


namespace Tagcade\Entity\Report\PerformanceReport\Display\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReport as AdNetworkDomainAdTagSubPublisherReportModel;

/**
 * Contains AdNetwork Site Ad Tag SubPublisher Performance report after mapping partner site domain ad tag with tagcade site domain ad tag subPublisher
 * @package Tagcade\Entity\Report\PerformanceReport\Display\Partner
 */
class AdNetworkDomainAdTagSubPublisherReport extends AdNetworkDomainAdTagSubPublisherReportModel
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
    protected $subPublisher;
}