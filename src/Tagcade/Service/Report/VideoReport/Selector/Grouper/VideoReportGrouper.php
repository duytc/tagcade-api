<?php

namespace Tagcade\Service\Report\VideoReport\Selector\Grouper;


use Tagcade\Exception\UnexpectedValueException;
use Tagcade\Model\Report\VideoReport\AdTagReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers\WaterfallTagGrouper;
use Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers\DefaultGrouper;
use Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers\GrouperInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportResultInterface;

class VideoReportGrouper implements VideoReportGrouperInterface
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

        return new WaterfallTagGrouper($reportCollection);
    }
} 