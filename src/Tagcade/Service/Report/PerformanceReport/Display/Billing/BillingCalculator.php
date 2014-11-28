<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class BillingCalculator implements BillingCalculatorInterface
{
    protected $defaultCpmRate;
    /**
     * @var BillingRateThreshold[]
     */
    protected $defaultBillingThresholds;

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
     */
    public function __construct($defaultCpmRate = 0.0025, array $defaultBilledThresholds = [])
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
    }

    /**
     * @inheritdoc
     */
    public function calculateBilledAmountForPublisher(PublisherInterface $publisher, $slotOpportunities)
    {
        if (!is_int($slotOpportunities)) {
            throw new InvalidArgumentException('Slot opportunities must be a number');
        }

        $cpmRate = $this->getCustomCpmRateForPublisher($publisher);

        if (null !== $cpmRate) {
            return $this->calculateBilledAmount($cpmRate, $slotOpportunities);
        }

        $cpmRate = $this->findDefaultCpmRate($slotOpportunities);

        return $this->calculateBilledAmount($cpmRate, $slotOpportunities);

    }

    protected function getCustomCpmRateForPublisher(PublisherInterface $publisher)
    {
        // TODO: finish when multi-user integrated.
        return $publisher->getUser()->getBillingRate();
    }

    /**
     * @param float $cpmRate
     * @param int $slotOpportunities
     * @return float
     */
    protected function calculateBilledAmount($cpmRate, $slotOpportunities)
    {
        if (!is_numeric($cpmRate)) {
            throw new InvalidArgumentException('cpmRate must be a number');
        }

        return (float) $cpmRate * $slotOpportunities;
    }

    /**
     * @param int $slotOpportunities
     * @return float
     */
    protected function findDefaultCpmRate($slotOpportunities)
    {
        foreach($this->defaultBillingThresholds as $threshold) {
            if($slotOpportunities >= $threshold->getThreshold()) {
                return $threshold->getCpmRate();
            }
        }

        return $this->defaultCpmRate;
    }
}