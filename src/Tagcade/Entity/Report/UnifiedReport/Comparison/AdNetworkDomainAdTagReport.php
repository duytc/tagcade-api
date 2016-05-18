<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReport as AdNetworkDomainAdTagReportModel;

class AdNetworkDomainAdTagReport extends AdNetworkDomainAdTagReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $domain;
    protected $partnerTagId;
    protected $adNetwork;
    protected $performanceAdNetworkDomainAdTagReport;
    protected $unifiedAdNetworkDomainAdTagReport;
}