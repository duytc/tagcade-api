<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper;

use Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint\DefaultGrouper as PulsePointDefaultGrouper;
use Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint\GrouperInterface;
use Tagcade\Service\Report\UnifiedReport\Result\UnifiedReportResultInterface;

class ReportGrouper implements ReportGrouperInterface
{
    public function groupReports(UnifiedReportResultInterface $reportCollection)
    {
        $grouper = static::group($reportCollection);

        return $grouper->getGroupedReport();
    }

    /**
     * Factory pattern to return a grouper
     *
     * @param UnifiedReportResultInterface $reportCollection
     * @return GrouperInterface
     */
    public static function group(UnifiedReportResultInterface $reportCollection)
    {
        $reports = $reportCollection->getReports();

        // get first report in array, use it to determine the grouper method
        // TODO pick report grouper base on report type or report item

        return new PulsePointDefaultGrouper($reportCollection);
    }
} 