<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface as UnifiedSubPublisherReportRepositoryInterface;

interface SubPublisherReportRepositoryInterface extends UnifiedSubPublisherReportRepositoryInterface
{
    /**
     * @param SubPublisherReportInterface $report
     * @return mixed
     */
    public function override(SubPublisherReportInterface $report);
}