<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as ReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;

class ProjectedBillingCalculator implements ProjectedBillingCalculatorInterface
{
    /**
     * @var AccountReportRepositoryInterface
     */
    protected $accountReportRepository;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;
    /**
     * @var SiteRepositoryInterface
     */
    protected $siteReportRepository;


    function __construct(AccountReportRepositoryInterface $accountReportRepository, SiteReportRepositoryInterface $siteReportRepository, DateUtilInterface $dateUtil)
    {
        $this->accountReportRepository = $accountReportRepository;
        $this->siteReportRepository = $siteReportRepository;
        $this->dateUtil = $dateUtil;
    }

    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        $billedAmountUpToYesterday = $this->accountReportRepository->getSumBilledAmountForPublisher(
            $publisher,
            $this->dateUtil->getFirstDateInMonth(),
            new DateTime('yesterday')
        );

        $dayAverageBilledAmount = $billedAmountUpToYesterday / $this->dateUtil->getNumberOfDatesPassedInMonth();
        $projectedBilledAmount = $billedAmountUpToYesterday +
            ($dayAverageBilledAmount * ($this->dateUtil->getNumberOfRemainingDatesInMonth() + 1)); // +1 to include today

        return $projectedBilledAmount;
    }

    public function calculateProjectedBilledAmountForSite(SiteInterface $site)
    {
        $billedAmountUpToYesterday = $this->siteReportRepository->getSumBilledAmountForSite(
            $site,
            $this->dateUtil->getFirstDateInMonth(),
            new DateTime('yesterday')
        );

        $dayAverageBilledAmount = $billedAmountUpToYesterday / $this->dateUtil->getNumberOfDatesPassedInMonth();
        $projectedBilledAmount = $billedAmountUpToYesterday +
            ($dayAverageBilledAmount * ($this->dateUtil->getNumberOfRemainingDatesInMonth() + 1)); // +1 to include today

        return $projectedBilledAmount;
    }


}