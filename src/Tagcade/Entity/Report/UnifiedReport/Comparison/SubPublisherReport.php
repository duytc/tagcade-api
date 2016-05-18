<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherReport as SubPublisherReportModel;

class SubPublisherReport extends SubPublisherReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $subPublisher;
    protected $performanceSubPublisherReport;
    protected $unifiedSubPublisherReport;
}