<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReport as AdNetworkDomainAdTagSubPublisherReportModel;

class AdNetworkDomainAdTagSubPublisherReport extends AdNetworkDomainAdTagSubPublisherReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $domain;
    protected $partnerTagId;
    protected $adNetwork;
    protected $subPublisher;
    protected $performanceAdNetworkDomainAdTagSubPublisherReport;
    protected $unifiedAdNetworkDomainAdTagSubPublisherReport;
}