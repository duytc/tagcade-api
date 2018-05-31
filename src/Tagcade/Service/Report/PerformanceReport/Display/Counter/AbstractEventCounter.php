<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;

abstract class AbstractEventCounter implements EventCounterInterface
{
    /**
     * @var DateTime
     */
    protected $date;

    protected $dataWithDateHour;

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
            $this->date = new DateTime('today');
        }

        return $this->date;
    }

    public function setDataWithDateHour($dataWithDateHour)
    {
        $this->dataWithDateHour = $dataWithDateHour;
    }

    public function getDataWithDateHour()
    {
        return $this->dataWithDateHour;
    }
}