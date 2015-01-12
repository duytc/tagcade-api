<?php

namespace Tagcade\Domain\DTO\Statistics\Summary;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountSummary
{

    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var DateTime
     */
    private $month;
    /**
     * @var Summary
     */
    private $summary;


    function __construct(PublisherInterface $publisher, DateTime $month, Summary $summary)
    {
        $this->publisher = $publisher;
        $this->month = $month->format('Y-m');
        $this->summary = $summary;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return DateTime
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return Summary
     */
    public function getSummary()
    {
        return $this->summary;
    }

} 