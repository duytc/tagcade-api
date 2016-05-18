<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainReport as AdNetworkDomainReportModel;

class AdNetworkDomainReport extends AdNetworkDomainReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $domain;
    protected $adNetwork;
    protected $performanceAdNetworkDomainReport;
    protected $unifiedNetworkSiteReport;
}