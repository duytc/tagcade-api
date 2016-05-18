<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkReport as AdNetworkReportModel;

class AdNetworkReport extends AdNetworkReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $adNetwork;
    protected $performanceAdNetworkReport;
    protected $unifiedAdNetworkReport;
}