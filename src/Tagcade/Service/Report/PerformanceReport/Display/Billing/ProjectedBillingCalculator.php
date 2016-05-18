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


    function __construct(AccountReportRepositoryInterface $accountReportRepository, SiteReportRepositoryInterface $siteReportRepository, CpmRateGetterInterface $cpmRateGetter, DateUtilInterface $dateUtil)
    {
        $this->accountReportRepository = $accountReportRepository;
        $this->siteReportRepository = $siteReportRepository;
        $this->dateUtil = $dateUtil;
        $this->cpmRateGetter = $cpmRateGetter;
    }

    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        $projectedSlotOpportunities = $this->calculatePublisherProjectedSlotOpportunities($publisher);

        $publisherCpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, AbstractUser::MODULE_DISPLAY, $projectedSlotOpportunities)->getCpmRate();

        return $this->calculateBilledAmount($publisherCpmRate, $projectedSlotOpportunities);
    }

    public function calculateProjectedBilledAmountForSite(SiteInterface $site)
    {
        $siteProjectedSlotOpportunities = $this->calculateSiteProjectedSlotOpportunities($site);

        $publisher = $site->getPublisher();

        $publisherCpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, AbstractUser::MODULE_DISPLAY, $siteProjectedSlotOpportunities)->getCpmRate();

        return $this->calculateBilledAmount($publisherCpmRate, $siteProjectedSlotOpportunities);
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