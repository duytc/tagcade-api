<?php

namespace Tagcade\Service\Report\SourceReport\Selector\Grouper;

use Tagcade\Service\Report\SourceReport\Result\ReportResultInterface;
use Tagcade\Service\Report\SourceReport\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\SourceReport\Selector\Grouper\Groupers\GrouperInterface;

class ReportGrouper implements ReportGrouperInterface

{
    public function groupReports(ReportResultInterface $reportCollection)
    {
        $grouper = static::group($reportCollection);

        return $grouper->getGroupedReport();
    }

    /**
     * Factory pattern to return a grouper
     *
     * @param ReportResultInterface $reportCollection
     * @return GrouperInterface
     */
    public static function group(ReportResultInterface $reportCollection)
    {
        return new DefaultGrouper($reportCollection);
    }
}