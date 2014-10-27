<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;

use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

trait HasSubReportsTrait
{
    /**
     * @var \Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface
     */
    protected $subReportCreator;

    protected function syncEventCounterForSubReports()
    {
        $this->subReportCreator->setEventCounter($this->getEventCounter());
    }

    /**
     * @return EventCounterInterface
     */
    abstract public function getEventCounter();
}