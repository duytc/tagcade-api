<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Grouper;

use Tagcade\Exception\UnexpectedValueException;
use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Service\Report\RtbReport\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\RtbReport\Selector\Grouper\Groupers\GrouperInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;

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

        return new DefaultGrouper($reportCollection);
    }
} 