<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkAdTagReport as AdNetworkAdTagReportModel;

class AdNetworkAdTagReport extends AdNetworkAdTagReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $partnerTagId;
    protected $adNetwork;
    protected $performanceAdNetworkAdTagReport;
    protected $unifiedAdNetworkAdTagReport;
}