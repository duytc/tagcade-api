<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;

abstract class RtbCreatorAbstract implements RtbCreatorInterface
{
    /**
     * @var RtbEventCounterInterface|null
     */
    protected $eventCounter;

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->getEventCounter()->getDate();
    }

    /**
     * @inheritdoc
     */
    public function setEventCounter(RtbEventCounterInterface $eventCounter)
    {
        $this->eventCounter = $eventCounter;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEventCounter()
    {
        if (!$this->hasEventCounter()) {
            throw new RuntimeException('eventCounter was not set');
        }

        return $this->eventCounter;
    }

    protected function hasEventCounter()
    {
        return $this->eventCounter instanceof RtbEventCounterInterface;
    }

    /**
     * @inheritdoc
     */
    public function createReport(ReportTypeInterface $reportType)
    {
        return $this->doCreateReport($reportType);
    }
}