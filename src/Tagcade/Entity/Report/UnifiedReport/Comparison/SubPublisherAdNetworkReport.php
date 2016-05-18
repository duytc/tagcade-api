<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReport as SubPublisherAdNetworkReportModel;

class SubPublisherAdNetworkReport extends SubPublisherAdNetworkReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $subPublisher;
    protected $adNetwork;
    protected $performanceSubPublisherAdNetworkReport;
    protected $unifiedSubPublisherAdNetworkReport;
}