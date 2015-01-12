<?php

namespace Tagcade\Domain\DTO\Statistics;

use Tagcade\Model\User\Role\PublisherInterface;

class PublisherBilledAmount
{
    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var MonthBilledAmount
     */
    private $monthBilledAmount;


    function __construct(PublisherInterface $publisher, MonthBilledAmount $monthBilledAmount)
    {
        $this->publisher = $publisher;
        $this->monthBilledAmount = $monthBilledAmount;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return MonthBilledAmount
     */
    public function getMonthBilledAmount()
    {
        return $this->monthBilledAmount;
    }
}