<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;

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
            });

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

    public function getBilledRateForPublisher(PublisherInterface $publisher, DateTime $date = null)
    {
        if ( null !== $publisher->getUser()->getBillingRate()) {
            return $publisher->getUser()->getBillingRate();
        }

        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $monthSlotOpportunities = $this->accountReportRepository->getSumSlotOpportunities(
            $publisher,
            $this->dateUtil->getFirstDateInMonth($date),
            $this->dateUtil->getLastDateInMonth($date, true)
        );

        return $this->getDefaultCpmRate($monthSlotOpportunities);
    }

    public function getLastRateForPublisher(PublisherInterface $publisher)
    {
        // TODO return current rate of publisher

        return $publisher->getUser()->getBillingRate();
    }
}