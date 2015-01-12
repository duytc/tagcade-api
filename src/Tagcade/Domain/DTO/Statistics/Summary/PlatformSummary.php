<?php

namespace Tagcade\Domain\DTO\Statistics\Summary;

use DateTime;

class PlatformSummary {

    /**
     * @var DateTime
     */
    private $month;
    /**
     * @var Summary
     */
    private $summary;

    function __construct(DateTime $month, Summary $summary)
    {
        $this->month = $month->format('Y-m');
        $this->summary = $summary;
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