<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
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

    /** @var BillingConfigurationRepositoryInterface */
    private $billingConfigurationRepository;

    function __construct(CpmRateGetterInterface $defaultRateGetter, AccountReportRepositoryInterface $accountReportRepository,
         AccountHeaderBiddingReportRepositoryInterface $accountHeaderBiddingReportRepository, DateUtilInterface $dateUtil, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        $this->cpmRateGetter = $defaultRateGetter;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
        $this->accountHeaderBiddingReportRepository = $accountHeaderBiddingReportRepository;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
    }

    public function calculateBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $newWeight)
    {
        if (!is_numeric($newWeight)) {
            $newWeight = 0;
        }

        if ($newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($date);
        $yesterday = date_create($date->format('Y-m-d'))->modify('-1 day');

        /** @var BillingConfigurationInterface $billingConfiguration */
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, $module = User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        if ($billingConfiguration->getBillingFactor() == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $this->accountReportRepository->getSumImpressionOpportunities($publisher, $firstDateInMonth, $yesterday);
        } else {
            $weight = $this->accountReportRepository->getSumSlotOpportunities($publisher, $firstDateInMonth, $yesterday);
        }

        $weight += $newWeight;
        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, User::MODULE_DISPLAY, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }

    public function calculateHbBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $newWeight)
    {
        if (!is_numeric($newWeight)) {
            $newWeight = 0;
        }

        if ($newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($date);
        $yesterday = date_create($date->format('Y-m-d'))->modify('-1 day');
        $weight = $this->accountHeaderBiddingReportRepository->getSumSlotHbRequests($publisher, $firstDateInMonth, $yesterday );

        $weight += $newWeight;
        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, User::MODULE_HEADER_BIDDING, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }

    public function calculateInBannerBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $newWeight)
    {
        if (!is_numeric($newWeight)) {
            $newWeight = 0;
        }

        if ($newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($date);
        $yesterday = date_create($date->format('Y-m-d'))->modify('-1 day');
        $weight = $this->accountReportRepository->getSumSlotInBannerImpressions($publisher, $firstDateInMonth, $yesterday);

        $weight += $newWeight;
        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, User::MODULE_IN_BANNER, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }
}