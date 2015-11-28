<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportRevenueInterface;
use Tagcade\Model\Report\UnifiedReport\UnifiedReportModelInterface;
use Tagcade\Service\Report\UnifiedReport\Grouper\PulsePoint;
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
     * @throws NotSupportedException
     * @return GrouperInterface
     */
    public static function group(UnifiedReportResultInterface $reportCollection)
    {
        $reports = $reportCollection->getReports();

        $report = current($reports); // current() return false if $reports is empty!!!

        if (!$report) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        if ($report instanceof PulsePointUnifiedReportRevenueInterface) {
            return new PulsePoint\RevenueGrouper($reportCollection);
        } elseif ($report instanceof UnifiedReportModelInterface) {
            return new PulsePoint\DefaultGrouper($reportCollection);
        }

        throw new NotSupportedException(sprintf('Not support grouping for this report result %s', get_class($report)));
    }
} 