<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\SubPublisher as SubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\SubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher\SubPublisher as UnifiedSubPublisherSelector;

class SubPublisher extends UnifiedSubPublisherSelector
{
    /**
     * @var SubPublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherReportType;
    }
}