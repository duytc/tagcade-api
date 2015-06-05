<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper;

use Tagcade\Exception\UnexpectedValueException;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\BilledReportGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\GrouperInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\ImpressionBreakdownGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

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
        $reports = $reportCollection->getReports();

        // get first report in array, use it to determine the grouper method
        $firstReport = reset($reports);

        if (!$firstReport instanceof ReportDataInterface) {
            throw new UnexpectedValueException('Expected a ReportDataInterface');
        }

        if ($firstReport instanceof BilledReportDataInterface) {
            return new BilledReportGrouper($reportCollection);
        }

        if($firstReport instanceof ImpressionBreakdownReportDataInterface) {
            return new ImpressionBreakdownGrouper($reportCollection);
        }


        return new DefaultGrouper($reportCollection);
    }
} 