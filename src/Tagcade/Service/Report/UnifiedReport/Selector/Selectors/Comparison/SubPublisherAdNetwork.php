<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\SubPublisherAdNetwork as SubPublisherAdNetworkReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher\SubPublisherNetwork as UnifiedSubPublisherAdNetworkSelector;

class SubPublisherAdNetwork extends UnifiedSubPublisherAdNetworkSelector
{
    /**
     * @var SubPublisherAdNetworkReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherAdNetworkReportType;
    }
}