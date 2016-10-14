<?php

namespace Tagcade\Service\Report\SourceReport\Billing;


use DateTime;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Domain\DTO\Report\BillingRateThreshold;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoAccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;

class CpmRateGetter implements CpmRateGetterInterface
{
    const BILLING_FACTOR_SLOT_OPPORTUNITY = 'SLOT_OPPORTUNITY';
    const BILLING_FACTOR_VIDEO_IMPRESSION = 'VIDEO_IMPRESSION';
    const BILLING_FACTOR_VIDEO_VISIT = 'VISIT';

    protected $defaultCpmRate;

    /** @var BillingRateThreshold[] */
    protected $defaultBillingThresholds;

    /** @var AccountReportRepositoryInterface */
    private $accountReportRepository;

    /** @var ReportRepositoryInterface */
    private $reportRepository;
    /**
     * @var VideoAccountReportRepositoryInterface
     */
    private $videoAccountReportRepository;

    /** @var DateUtilInterface */
    private $dateUtil;

    /** @var BillingConfigurationRepositoryInterface */
    private $billingConfigurationRepository;

    /**
     * CpmRateGetter constructor.
     * @param array $defaultBilledThresholds
     * @param AccountReportRepositoryInterface $accountReportRepository
     * @param DateUtilInterface $dateUtil
     * @param BillingConfigurationRepositoryInterface $billingConfigurationRepository
     * @param ReportRepositoryInterface $reportRepository
     * @param VideoAccountReportRepositoryInterface $videoAccountReportRepository
     */
    public function __construct(array $defaultBilledThresholds = [],
                                AccountReportRepositoryInterface $accountReportRepository, DateUtilInterface $dateUtil,
                                BillingConfigurationRepositoryInterface $billingConfigurationRepository,
                                ReportRepositoryInterface $reportRepository,
                                VideoAccountReportRepositoryInterface $videoAccountReportRepository
    )
    {
        foreach ($defaultBilledThresholds as $threshold) {
            if (!$threshold instanceof BillingRateThreshold) {
                throw new InvalidArgumentException('Invalid array of thresholds');
            }

            unset($threshold);
        }

        // sort thresholds, descending order
        usort($defaultBilledThresholds, function (BillingRateThreshold $a, BillingRateThreshold $b) {
            if ($a->getThreshold() === $b->getThreshold()) {
                return 0;
            }

            return ($a->getThreshold() < $b->getThreshold()) ? -1 : 1;
        }
        );

        $this->defaultBillingThresholds = $defaultBilledThresholds;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
        $this->reportRepository = $reportRepository;
        $this->videoAccountReportRepository = $videoAccountReportRepository;
    }

    public static function createConfig(array $thresholds)
    {
        $config = [];

        foreach ($thresholds as $threshold) {
            if (!isset($threshold['threshold']) || !isset($threshold['cpmRate'])) {
                throw new InvalidArgumentException('Cannot create configuration. Missing required threshold or rate');
            }

            $config[] = new BillingRateThreshold($threshold['threshold'], $threshold['cpmRate']);
        }

        return $config;
    }
    
    
    public function getDefaultCpmRate($weight)
    {
        // get default cmpRate
        $cpmRate = reset($this->defaultBillingThresholds)->getCpmRate();
        foreach ($this->defaultBillingThresholds as $threshold) {
            if ($weight < $threshold->getThreshold()) {
                break;
            }

            $cpmRate = $threshold->getCpmRate();
        }

        return $cpmRate;
    }

    public function getBillingWeightForSiteInMonthBeforeDate(SiteInterface $site, $module, DateTime $date)
    {
        $publisher = $site->getPublisher();
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, $module);

        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(self::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }

        $billingFactor = $billingConfiguration->getBillingFactor();
        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($date);
        $date = $date->modify('-1 day');

        switch ($billingFactor) {
            case self::BILLING_FACTOR_SLOT_OPPORTUNITY:
                return $this->accountReportRepository->getSumSlotOpportunities($publisher, $firstDateInMonth, $date);
            case self::BILLING_FACTOR_VIDEO_IMPRESSION:
                if ($billingConfiguration->getModule() === User::MODULE_VIDEO) {
                    return $this->videoAccountReportRepository->getSumVideoImpressionsForPublisher($publisher, $firstDateInMonth, $date);
                }

                return $this->reportRepository->getTotalVideoImpressionForSite($site, $firstDateInMonth, $date);
            case self::BILLING_FACTOR_VIDEO_VISIT:
                return $this->reportRepository->getTotalVideoVisitForSite($site, $firstDateInMonth, $date);
            default:
                throw new \Exception(sprintf('Do not support this billing factor yet %s', $billingFactor));
        }
    }

    public function getCpmRateForPublisher(PublisherInterface $publisher, $module, $weight)
    {
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, $module);

        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            return new CpmRate(0, true); // Not found any billing configuration => not bill this module then cpm = 0
        }

        if ($billingConfiguration->isDefaultConfiguration()) {
            return new CpmRate($this->getDefaultCpmRate($weight));
        }

        return new CpmRate($billingConfiguration->getCpmRate($weight));
    }
}