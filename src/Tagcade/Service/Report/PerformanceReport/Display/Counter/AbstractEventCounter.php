<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;

abstract class AbstractEventCounter implements EventCounterInterface
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date = null)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        if (!$this->date) {
            return new DateTime('today');
        }

        return $this->date;
    }
}