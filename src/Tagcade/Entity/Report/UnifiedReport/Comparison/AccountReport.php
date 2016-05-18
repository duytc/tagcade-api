<?php


namespace Tagcade\Entity\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AccountReport as AccountReportModel;

class AccountReport extends AccountReportModel
{
    protected $id;

    protected $date;
    protected $name;

    protected $publisher;
    protected $performanceAccountReport;
    protected $unifiedAccountReport;
}