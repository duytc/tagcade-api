<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Entity\Core\BillingConfiguration;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
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

    /** @var DateUtilInterface */
    private $dateUtil;

    /** @var BillingConfigurationRepositoryInterface */
    private $billingConfigurationRepository;

    /**
     * @param float $defaultCpmRate
     * @param BillingRateThreshold[] $defaultBilledThresholds
     * @param AccountReportRepositoryInterface $accountReportRepository
     * @param DateUtilInterface $dateUtil
     * @param BillingConfigurationRepositoryInterface $billingConfigurationRepository
     * @param ReportRepositoryInterface $reportRepository
     */
    public function __construct($defaultCpmRate = 0.0025, array $defaultBilledThresholds = [],
                                AccountReportRepositoryInterface $accountReportRepository, DateUtilInterface $dateUtil,
                                BillingConfigurationRepositoryInterface $billingConfigurationRepository,
                                ReportRepositoryInterface $reportRepository
    )
    {
        if (!is_numeric($defaultCpmRate)) {
            throw new InvalidArgumentException('Invalid default cpm rate');
        }

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

                return ($a->getThreshold() > $b->getThreshold()) ? -1 : 1;
            }
        );

        $this->defaultCpmRate = (float)$defaultCpmRate;
        $this->defaultBillingThresholds = $defaultBilledThresholds;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
        $this->reportRepository = $reportRepository;

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
        foreach ($this->defaultBillingThresholds as $threshold) {
            if ($weight >= $threshold->getThreshold()) {
                return $threshold->getCpmRate();
            }
        }

        return $this->defaultCpmRate;
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

    public function getCpmRateForPublisherByMonth(PublisherInterface $publisher, $module, DateTime $month)
    {
        $weight = $this->getBillingWeightForPublisherByMonth($publisher, $module, $month);

        return $this->getCpmRateForPublisher($publisher, $module, $weight);
    }

    /**
     * get weight count for a specific publisher with given module in a month
     *
     * @param PublisherInterface $publisher
     * @param $module
     * @param DateTime $month
     * @throws \Exception
     * @return null when no billing configuration found
     *         int when at least one configuration found
     */
    protected function getBillingWeightForPublisherByMonth(PublisherInterface $publisher, $module, DateTime $month)
    {
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, $module);

        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(self::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }

        $billingFactor = $billingConfiguration->getBillingFactor();
        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($month);
        $lastDateInMonth = $this->dateUtil->getLastDateInMonth($month, true);

        switch ($billingFactor) {
            case self::BILLING_FACTOR_SLOT_OPPORTUNITY:
                return $this->accountReportRepository->getSumSlotOpportunities($publisher, $firstDateInMonth, $lastDateInMonth);
            case self::BILLING_FACTOR_VIDEO_IMPRESSION:
                return $this->reportRepository->getTotalVideoImpressionForPublisher($publisher, $firstDateInMonth, $lastDateInMonth);
            case self::BILLING_FACTOR_VIDEO_VISIT:
                return $this->reportRepository->getTotalVideoVisitForPublisher($publisher, $firstDateInMonth, $lastDateInMonth);
            default:
                throw new \Exception(sprintf('Do not support this billing factor yet %s', $billingFactor));
        }
    }
}