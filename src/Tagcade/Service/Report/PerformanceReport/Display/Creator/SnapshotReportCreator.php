<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class SnapshotReportCreator extends ReportCreatorAbstract implements SnapshotReportCreatorInterface
{
    /**
     * @param CreatorInterface[] $creators
     * @param EventCounterInterface $eventCounter
     */
    public function __construct(array $creators, EventCounterInterface $eventCounter)
    {
        parent::__construct($creators, $eventCounter);
    }

    protected function addCreator(CreatorInterface $creator)
    {
        if (!$creator instanceof SnapshotCreatorInterface) {
            throw new InvalidArgumentException('Require SnapshotCreatorInterface for SnapshotReportCreator');
        }

        parent::addCreator($creator);
    }
}