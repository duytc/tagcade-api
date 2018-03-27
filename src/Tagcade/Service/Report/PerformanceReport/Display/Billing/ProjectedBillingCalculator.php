<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as ReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User as AbstractUser;


class ProjectedBillingCalculator implements ProjectedBillingCalculatorInterface
{
    use CalculateBilledAmountTrait;

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
    /**
     * @var CpmRateGetterInterface
     */
    private $cpmRateGetter;

    /** @var BillingConfigurationRepositoryInterface  */
    private $billingConfigurationRepository;

    function __construct(AccountReportRepositoryInterface $accountReportRepository, SiteReportRepositoryInterface $siteReportRepository, CpmRateGetterInterface $cpmRateGetter, DateUtilInterface $dateUtil, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        $this->accountReportRepository = $accountReportRepository;
        $this->siteReportRepository = $siteReportRepository;
        $this->dateUtil = $dateUtil;
        $this->cpmRateGetter = $cpmRateGetter;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
    }

    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $this->calculatePublisherProjectedImpressionOpportunities($publisher);
        } else {
            $weight = $this->calculatePublisherProjectedSlotOpportunities($publisher);
        }

        $publisherCpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, AbstractUser::MODULE_DISPLAY, $weight)->getCpmRate();

        return $this->calculateBilledAmount($publisherCpmRate, $weight);
    }

    public function calculateProjectedBilledAmountForSite(SiteInterface $site)
    {
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($site->getPublisher(), User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $this->calculateSiteProjectedImpressionOpportunities($site);
        } else {
            $weight = $this->calculateSiteProjectedSlotOpportunities($site);
        }

        $publisher = $site->getPublisher();

        $publisherCpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, AbstractUser::MODULE_DISPLAY, $weight)->getCpmRate();

        return $this->calculateBilledAmount($publisherCpmRate, $weight);
    }

    protected function calculateSiteProjectedSlotOpportunities(SiteInterface $site)
    {
        // Step 1. Get SlotOpportunities up to today
        $date = new DateTime('yesterday');
        $currentSlotOpportunities = (int)$this->siteReportRepository->getSumSlotOpportunities(
            $site,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->calculateProjectedSlotOpportunities($currentSlotOpportunities);
    }

    protected function calculateSiteProjectedImpressionOpportunities(SiteInterface $site)
    {
        // Step 1. Get ImpressionOpportunities up to today
        $date = new DateTime('yesterday');
        $currentSlotOpportunities = (int)$this->siteReportRepository->getSumImpressionOpportunities(
            $site,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->calculateProjectedSlotOpportunities($currentSlotOpportunities);
    }

    protected function calculatePublisherProjectedSlotOpportunities(PublisherInterface $publisher)
    {
        // Step 1. Get SlotOpportunities up to today
        $date = new DateTime('yesterday');
        $currentSlotOpportunities = (int)$this->accountReportRepository->getSumSlotOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->calculateProjectedSlotOpportunities($currentSlotOpportunities);
    }

    protected function calculatePublisherProjectedImpressionOpportunities(PublisherInterface $publisher)
    {
        // Step 1. Get ImpressionOpportunities up to today
        $date = new DateTime('yesterday');
        $currentSlotOpportunities = (int)$this->accountReportRepository->getSumImpressionOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->calculateProjectedSlotOpportunities($currentSlotOpportunities);
    }

    protected function calculateProjectedSlotOpportunities($currentSlotOpportunities)
    {
        // Step 1. Calculate daily average
        $dayAverageSlotOpportunities = $currentSlotOpportunities / $this->dateUtil->getNumberOfDatesPassedInMonth();

        // Step 2. Projected SlotOpportunities equals to sum of estimated slotOpportunities and current SlotOpportunities
        $projectedSlotOpportunities = $currentSlotOpportunities +
            ($dayAverageSlotOpportunities * ($this->dateUtil->getNumberOfRemainingDatesInMonth() + 1)); // +1 to include today

        return $projectedSlotOpportunities;
    }
}