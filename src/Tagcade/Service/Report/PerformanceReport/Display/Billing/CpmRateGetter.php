<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;

class CpmRateGetter implements CpmRateGetterInterface
{

    protected $defaultCpmRate;
    /**
     * @var BillingRateThreshold[]
     */
    protected $defaultBillingThresholds;
    /**
     * @var AccountReportRepositoryInterface
     */
    private $accountReportRepository;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;

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


    /**
     * @param float $defaultCpmRate
     * @param BillingRateThreshold[] $defaultBilledThresholds
     * @param AccountReportRepositoryInterface $accountReportRepository
     * @param DateUtilInterface $dateUtil
     */
    public function __construct($defaultCpmRate = 0.0025, array $defaultBilledThresholds = [], AccountReportRepositoryInterface $accountReportRepository, DateUtilInterface $dateUtil)
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
        usort($defaultBilledThresholds, function(BillingRateThreshold $a, BillingRateThreshold $b) {
                if ($a->getThreshold() === $b->getThreshold()) {
                    return 0;
                }

                return ($a->getThreshold() > $b->getThreshold()) ? -1 : 1;
            }
        );

        $this->defaultCpmRate = (float) $defaultCpmRate;
        $this->defaultBillingThresholds = $defaultBilledThresholds;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
    }

    public function getDefaultCpmRate($slotOpportunities)
    {
        foreach($this->defaultBillingThresholds as $threshold) {
            if($slotOpportunities >= $threshold->getThreshold()) {
                return $threshold->getCpmRate();
            }
        }

        return $this->defaultCpmRate;
    }

    public function getTodayCpmRateForPublisher(PublisherInterface $publisher, $todaySlotOpportunities = 0)
    {
        if (null !== $publisher->getBillingRate()) {
            return new CpmRate($publisher->getBillingRate(), true);
        }

        $date = new DateTime('yesterday');
        $currentSlotOpportunities = $this->accountReportRepository->getSumSlotOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date)
        );

        $totalSlotOpportunities = $currentSlotOpportunities + $todaySlotOpportunities;
        $cpmRate = $this->getDefaultCpmRate($totalSlotOpportunities);

        return new CpmRate($cpmRate);
    }


    public function getThresholdRateForPublisher(PublisherInterface $publisher, DateTime $date = null)
    {
        $monthSlotOpportunities = $this->accountReportRepository->getSumSlotOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->getDefaultCpmRate($monthSlotOpportunities);
    }


}