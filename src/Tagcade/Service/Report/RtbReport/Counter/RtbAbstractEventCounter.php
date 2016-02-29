<?php

namespace Tagcade\Service\Report\RtbReport\Counter;


abstract class RtbAbstractEventCounter implements RtbEventCounterInterface
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @inheritdoc
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        if (!$this->date) {
            $this->date = new \DateTime('today');
        }

        return $this->date;
    }
}