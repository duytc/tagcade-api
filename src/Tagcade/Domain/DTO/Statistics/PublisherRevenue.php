<?php

namespace Tagcade\Domain\DTO\Statistics;


use Tagcade\Model\User\Role\PublisherInterface;

class PublisherRevenue {

    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var MonthRevenue
     */
    private $monthRevenue;

    function __construct(PublisherInterface $publisher, MonthRevenue $monthRevenue)
    {
        $this->publisher = $publisher;
        $this->monthRevenue = $monthRevenue;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return MonthRevenue
     */
    public function getMonthRevenue()
    {
        return $this->monthRevenue;
    }


} 