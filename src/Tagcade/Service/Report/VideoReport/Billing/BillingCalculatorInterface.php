<?php


namespace Tagcade\Service\Report\VideoReport\Billing;


use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Model\User\Role\PublisherInterface;

interface BillingCalculatorInterface
{
    /**
     * @param DateTime $date
     * @param PublisherInterface $publisher
     * @param $module
     * @param $newWeight
     * @return RateAmount
     */
    public function calculateVideoBilledAmountForPublisherForSingleDay(DateTime $date, PublisherInterface $publisher, $module, $newWeight);
}