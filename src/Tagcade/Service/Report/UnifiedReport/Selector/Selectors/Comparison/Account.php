<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Comparison;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison\Account as AccountReportType;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AccountReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher\Publisher as UnifiedAccountSelector;

class Account extends UnifiedAccountSelector
{
    /**
     * @var AccountReportRepositoryInterface
     */
    protected $repository;

    /**
     * important: override parents to make sure $reportType is correct!!!
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }
}