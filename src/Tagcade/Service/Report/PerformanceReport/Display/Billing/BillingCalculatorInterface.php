<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Model\User\Role\PublisherInterface;

interface BillingCalculatorInterface
{
    /**
     * @param DateTime $date
     * @param PublisherInterface $publisher
     * @param $module
     * @param $weight
     * @return RateAmount
     */
    public function calculateBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $module, $weight);

    /**
     * @param DateTime $date
     * @param PublisherInterface $publisher
     * @param $module
     * @param $weight
     * @return mixed
     */
    public function calculateHbBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $module, $weight);

    /**
     * @param DateTime $date
     * @param PublisherInterface $publisher
     * @param $module
     * @param $weight
     * @return RateAmount
     */
    public function calculateInBannerBilledAmountForPublisher(DateTime $date, PublisherInterface $publisher, $module, $weight);

    /**
     * @param float $cpmRate
     * @param int $slotOpportunities
     * @return float
     */
    public function calculateBilledAmount($cpmRate, $slotOpportunities);
}