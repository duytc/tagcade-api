<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as ReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

class ProjectedBillingCalculator implements ProjectedBillingCalculatorInterface
{
    /**
     * @var AccountReportRepositoryInterface
     */
    private $accountReportRepository;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;


    function __construct(AccountReportRepositoryInterface $accountReportRepository, DateUtilInterface $dateUtil)
    {
        $this->accountReportRepository = $accountReportRepository;
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
}