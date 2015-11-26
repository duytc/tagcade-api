<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors;

use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AccountManagementRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DailyReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DomainReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\SelectorInterface as UnifiedReportSelectorInterface;

abstract class AbstractSelector implements UnifiedReportSelectorInterface
{
    /** @var AccountManagementRepositoryInterface */
    protected $accountManagementRepository;

    /** @var DailyReportRepositoryInterface */
    protected $dailyReportRepository;

    /** @var DomainReportRepositoryInterface */
    protected $domainReportRepository;

    function __construct($accountManagementRepository, $dailyReportRepository, $domainReportRepository)
    {
        $this->accountManagementRepository = $accountManagementRepository;
        $this->dailyReportRepository = $dailyReportRepository;
        $this->domainReportRepository = $domainReportRepository;
    }

    public function supportsReportType()
    {
        // TODO: Implement supportsReportType() method.
    }
}