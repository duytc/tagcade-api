<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface as UnifiedSubPublisherAdNetworkReportRepositoryInterface;

interface SubPublisherAdNetworkReportRepositoryInterface extends UnifiedSubPublisherAdNetworkReportRepositoryInterface
{
    /**
     * @param SubPublisherAdNetworkReportInterface $report
     * @return mixed
     */
    public function override(SubPublisherAdNetworkReportInterface $report);
}