<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;
use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\DefaultGrouper;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\Hierarchy\Platform\CalculatedReportGrouper;
use Tagcade\Service\Report\PerformanceReport\Display\Grouper\Groupers\GrouperInterface;

class ReportGrouper implements ReportGrouperInterface
{
    /**
     * @inheritdoc
     */
    public function groupReports(ReportCollection $reportCollection)
    {
        $grouper = static::group($reportCollection);

        return $grouper->getGroupedReport();
    }

    /**
     * Factory pattern to return a grouper
     *
     * @param ReportCollection $reportCollection
     * @return GrouperInterface
     */
    public static function group(ReportCollection $reportCollection)
    {
        $reportType = $reportCollection->getReportType();

        if ($reportType instanceof PlatformReportTypes\CalculatedReportTypeInterface) {
            return new CalculatedReportGrouper($reportCollection);
        }

        return new DefaultGrouper($reportCollection);
    }
} 