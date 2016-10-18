<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators;

use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

trait HasSubReportsTrait
{
    /**
     * @var \Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface
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