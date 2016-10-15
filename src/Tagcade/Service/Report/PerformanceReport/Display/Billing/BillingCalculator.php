<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportRepositoryInterface as AccountHeaderBiddingReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;

class BillingCalculator implements BillingCalculatorInterface
{
    use CalculateBilledAmountTrait;
    /**
     * @var CpmRateGetterInterface
     */
    private $cpmRateGetter;

    /**
     * @var AccountReportRepositoryInterface
     */
    private $accountReportRepository;

    /**
     * @var AccountHeaderBiddingReportRepositoryInterface
     */
    private $accountHeaderBiddingReportRepository;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;

    function __construct(CpmRateGetterInterface $defaultRateGetter, AccountReportRepositoryInterface $accountReportRepository,
                         AccountHeaderBiddingReportRepositoryInterface $accountHeaderBiddingReportRepository, DateUtilInterface $dateUtil)
    {
        $this->cpmRateGetter = $defaultRateGetter;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
        $this->accountHeaderBiddingReportRepository = $accountHeaderBiddingReportRepository;
    }

    public function calculateTodayBilledAmountForPublisher(PublisherInterface $publisher, $module, $newWeight)
    {
        if (!is_int($newWeight) || $newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $date = new DateTime('yesterday');
        $weight = $this->accountReportRepository->getSumSlotOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date)
        );

        $weight += $newWeight;
        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, $module, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }

    public function calculateTodayHbBilledAmountForPublisher(PublisherInterface $publisher, $module, $newWeight)
    {
        if (!is_int($newWeight) || $newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $date = new DateTime('yesterday');
        $weight = $this->accountHeaderBiddingReportRepository->getSumSlotHbRequests(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date)
        );

        $weight += $newWeight;
        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, $module, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }
}