<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\AdNetworkSubPublisher as AdNetworkSubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network\NetworkSubPublisher as UnifiedAdNetworkSubPublisherSelector;

class AdNetworkSubPublisher extends UnifiedAdNetworkSubPublisherSelector
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
        return $reportType instanceof AdNetworkSubPublisherReportType;
    }
}