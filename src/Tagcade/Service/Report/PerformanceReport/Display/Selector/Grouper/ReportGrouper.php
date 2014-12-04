<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper;

use Tagcade\Exception\UnexpectedValueException;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\BilledReportGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\GrouperInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollectionInterface;

class ReportGrouper implements ReportGrouperInterface
{
    public function groupReports(ReportCollectionInterface $reportCollection)
    {
        $grouper = static::group($reportCollection);

        return $grouper->getGroupedReport();
    }

    /**
     * Factory pattern to return a grouper
     *
     * @param ReportCollectionInterface $reportCollection
     * @return GrouperInterface
     */
    public static function group(ReportCollectionInterface $reportCollection)
    {
        $reports = $reportCollection->getReports();

        // get first report in array, use it to determine the grouper method
        $firstReport = reset($reports);

        if (!$firstReport instanceof ReportDataInterface) {
            throw new UnexpectedValueException('Expected a ReportDataInterface');
        }

        if ($firstReport instanceof BilledReportDataInterface) {
            return new BilledReportGrouper($reportCollection);
        }

        return new DefaultGrouper($reportCollection);
    }
} 