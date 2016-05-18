<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Tagcade\Model\Report\UnifiedReport\Comparison\AccountReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface as UnifiedAccountReportRepositoryInterface;

interface AccountReportRepositoryInterface extends UnifiedAccountReportRepositoryInterface
{
    /**
     * @param AccountReportInterface $report
     * @return mixed
     */
    public function override(AccountReportInterface $report);
}